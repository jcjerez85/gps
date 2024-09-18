<?php namespace ModalHelpers;

use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Validators\SendCommandFormValidator;
use CustomFacades\Validators\SendCommandGprsFormValidator;
use Illuminate\Support\Arr;
use Tobuli\Entities\Device;
use Tobuli\Entities\UserGprsTemplate;
use Tobuli\Entities\UserSmsTemplate;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Protocols\Commands;
use Tobuli\Services\Commands\CommandService;
use Tobuli\Services\Commands\SendCommandService;
use Validator;

class SendCommandModalHelper extends ModalHelper
{
    private $sendCommandService;

    public function __construct()
    {
        parent::__construct();

        $this->sendCommandService = new SendCommandService();
    }

    public function createData()
    {
        $this->checkException('send_command', 'view');

        $devices_gprs = $this->user->devices;
        $devices_sms = $this->user->devices_sms;

        $commands = [
            'engineStop' => trans('front.engine_stop'),
            'engineResume' => trans('front.engine_resume'),
            'alarmArm' => trans('front.alarm_arm'),
            'alarmDisarm' => trans('front.alarm_disarm'),
            'positionSingle' => trans('front.position_single'),
            'positionPeriodic' => trans('front.periodic_reporting'),
            'positionStop' => trans('front.stop_reporting'),
            'movementAlarm' => trans('front.movement_alarm'),
            'setTimezone' => trans('front.set_timezone'),
            'rebootDevice' => trans('front.reboot_device'),
            'sendSms' => trans('front.send_sms'),
            'requestPhoto' => trans('front.request_photo'),
            'custom' => trans('front.custom_command'),
        ];

        $units = [
            'second' => trans('front.second'),
            'minute' => trans('front.minute'),
            'hour' => trans('front.hour')
        ];

        $number_index = [
            '1' => trans('front.first'),
            '2' => trans('front.second'),
            '3' => trans('front.third'),
            '0' => trans('front.three_sos_numbers'),
        ];

        $actions = [
            '1' => trans('front.on'),
            '0' => trans('front.off'),
        ];

        if ($this->api) {
            $sms_templates = [['id' => '0', 'title' => trans('front.no_template'), 'message' => null]];
            $results = UserSmsTemplate::userAccessible($this->user)->orderBy('title')->get();
            foreach ($results as $row)
                array_push($sms_templates, ['id' => $row->id, 'title' => $row->title, 'message' => $row->message]);

            $gprs_templates = [['id' => '0', 'title' => trans('front.no_template'), 'message' => null]];
            $results = UserGprsTemplate::userAccessible($this->user)->orderBy('title')->get();
            foreach ($results as $row)
                array_push($gprs_templates, ['id' => $row->id, 'title' => $row->title, 'message' => $row->message]);

            $devices_sms_arr = [];
            foreach ($devices_sms as $device)
                array_push($devices_sms_arr, ['id' => $device->id, 'value' => $device->name]);
            $devices_sms = $devices_sms_arr;

            $devices_gprs_arr = [];
            foreach ($devices_gprs as $device)
                array_push($devices_gprs_arr, ['id' => $device->id, 'value' => $device->name]);
            $devices_gprs = $devices_gprs_arr;

            $commands = apiArray($commands);
            $units = apiArray($units);
            $number_index = apiArray($number_index);
            $actions = apiArray($actions);
        }
        else {
            $sms_templates = UserSmsTemplate::userAccessible($this->user)
                ->orderBy('title')
                ->pluck('title', 'id')
                ->prepend(trans('front.no_template'), '0')
                ->all();

            $gprs_templates = UserGprsTemplate::userAccessible($this->user)
                ->orderBy('title')
                ->pluck('title', 'id')
                ->prepend(trans('front.no_template'), '0')
                ->all();

            $devices_gprs = groupDevices($devices_gprs, $this->user);
            $devices_sms = groupDevices($devices_sms, $this->user);
        }

        $device_id = request()->get('id');

        $command_schedules = $this->user->commandSchedules;

        return compact('devices_sms', 'devices_gprs', 'sms_templates',
            'gprs_templates', 'commands', 'units', 'number_index', 'actions',
            'device_id', 'command_schedules');
    }

    public function create()
    {
        $this->checkException('send_command', 'view');

        $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : '';
        $this->data['message'] = isset($this->data['message_sms']) ? $this->data['message_sms'] : $this->data['message'];

        if ( ! $this->user->sms_gateway)
            throw new ValidationException(['id' => trans('front.sms_gateway_disabled')]);

        SendCommandFormValidator::validate('create', $this->data);

        $devices = $this->user->devices()->with('users')->findMany($this->data['devices']);

        $this->sendCommandService->sms($devices, $this->data['message'], $this->user);

        return $this->api ? ['status' => 1] : ['status' => 0, 'trigger' => 'send_command'];
    }

    public function gprsCreate()
    {
        $this->checkException('send_command', 'view');

        if (isset($this->data['device_id']) && !is_array($this->data['device_id']))
            $this->data['device_id'] = [$this->data['device_id']];

        SendCommandGprsFormValidator::validate('create', $this->data);

        $devices = $this->user->devices()->findMany($this->data['device_id']);

        $validator = Validator::make($this->data, Commands::validationRules(
            $this->data['type'],
            $this->getCommands($devices)
        ));

        if ($validator->fails())
            throw new ValidationException($validator->messages());

        $responses = $this->sendCommandService->gprs($devices, $this->data, $this->user);

        $errors = $responses
            ->filter(function ($response) {
                return $response['status'] == 0;
            })
            ->map(function ($response) {
                return "{$response['device']}: {$response['error']}";
            });

        if (count($errors) > 0) {
            if ($this->api)
                return ['status' => 1, 'error' => current($errors)];

            return [
                'status'   => 0,
                'trigger'  => 'send_command',
                'warnings' => $errors,
                'results' => $responses,
            ];
        }

        if ($this->api)
            return ['status' => 1, 'message' => trans('front.command_sent')];

        return [
            'status'  => 0,
            'trigger' => 'send_command',
            'message' => trans('front.command_sent') . ' ' . trans('global.successful'),
            'results' => $responses,
        ];
    }

    function getDeviceSimNumber()
    {
        $id = array_key_exists('device_id', $this->data) ? $this->data['device_id'] : $this->data['id'];

        $item = DeviceRepo::find($id);

        $this->checkException('devices', 'own', $item);

        return ['sim_number' => $item->sim_number];
    }

    function getDeviceCommands()
    {
        SendCommandGprsFormValidator::validate('commands', $this->data);

        if ( ! is_array($this->data['device_id']))
            $this->data['device_id'] = [$this->data['device_id']];

        $devices = Device::findMany($this->data['device_id']);

        return $this->getCommands($devices, true, Arr::get($this->data, 'connection'));
    }

    public function getCommands($devices, $intersect = false, $type = null)
    {
        if ($type == SendCommandService::CONNECTION_SMS)
            return (new CommandService($this->user))->getSmsCommands($devices, $intersect);

        return (new CommandService($this->user))->getGprsCommands($devices, $intersect);
    }
}