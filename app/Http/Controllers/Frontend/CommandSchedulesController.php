<?php namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\SendCommandModalHelper;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Repositories\UserSmsTemplateRepo;
use CustomFacades\Validators\SchedulesValidator;
use CustomFacades\Validators\SendCommandFormValidator;
use CustomFacades\Validators\SendCommandGprsFormValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tobuli\Entities\CommandSchedule;
use Tobuli\Entities\Device;
use Tobuli\Entities\SentCommand;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Protocols\Commands;
use Tobuli\Services\Commands\CommandService;
use Tobuli\Services\Commands\SendCommandService;
use Tobuli\Services\Schedule\Scheduler;

class CommandSchedulesController extends Controller
{
    private $scheduler;

    public function __construct(Scheduler $scheduler)
    {
        parent::__construct();

        $this->scheduler = $scheduler;

        $this->middleware(function ($request, $next) {
            $this->checkException('send_command', 'view');

            return $next($request);
        });
    }

    public function index()
    {
        return view('front::SendCommand.schedule.table', [
            'command_schedules' => $this->user->commandSchedules,
        ]);
    }

    public function create()
    {
        return view('front::SendCommand.schedule.create', SendCommandModalHelper::createData())
            ->with([
                'connections' => [
                    'gprs' => trans('front.gprs'),
                    'sms'  => trans('front.sms'),
                ],
            ]);
    }

    public function store()
    {
        SchedulesValidator::validate('create', request()->all());

        beginTransaction();
        try {
            if ($this->isCommandGprs(request('connection'))) {
                $command_schedule = $this->createGprs(request()->all());
            } else {
                $command_schedule = $this->createSms(request()->all());
            }

            $command_schedule->devices()->attach(
                $this->validDeviceIds(request('device_id') ?: request('devices'))
            );

            $this->scheduler->create($command_schedule, request()->all(), $this->user);
        } catch (ValidationException $e) {
            rollbackTransaction();
            throw $e;
        }

        commitTransaction();

        return ['status' => 1];
    }

    public function edit($id)
    {
        $commandSchedule = CommandSchedule::with(['schedule', 'devices'])->find($id);

        if ( ! $commandSchedule)
            throw new ResourseNotFoundException('front.command_schedule');

        $this->checkException(CommandSchedule::class, 'own', $commandSchedule);

        return view('front::SendCommand.schedule.edit', [
            'command_schedule' => $commandSchedule,
            'commands'         => SendCommandModalHelper::getCommands($commandSchedule->devices)->pluck('title', 'type')->all(),
            'devices_gprs'     => groupDevices($this->user->devices, $this->user),
            'devices_sms'      => groupDevices($this->user->devices_sms, $this->user),
            'sms_templates'    => UserSmsTemplateRepo::getWhere(['user_id' => $this->user->id], 'title')
                ->pluck('title', 'id')
                ->prepend(trans('front.no_template'), '0')
                ->all(),
            'connections'      => [
                'gprs' => trans('front.gprs'),
                'sms'  => trans('front.sms'),
            ],
        ]);
    }

    public function update($id)
    {
        SchedulesValidator::validate('update', request()->all());

        $command_schedule = CommandSchedule::find($id);

        if (is_null($command_schedule))
            throw new ResourseNotFoundException('front.command_schedule');

        $this->checkException(CommandSchedule::class, 'own', $command_schedule);

        beginTransaction();
        try {
            if ($this->isCommandGprs(request('connection'))) {
                $this->updateGprs($command_schedule, request()->all());
            } else {
                $this->updateSms($command_schedule, request()->all());
            }

            $command_schedule->devices()->sync(
                $this->validDeviceIds(request('device_id') ?: request('devices'))
            );

            $this->scheduler->update($command_schedule, request()->all());
        } catch (ValidationException $e) {
            rollbackTransaction();
            throw $e;
        }

        commitTransaction();

        return ['status' => 1];
    }

    public function destroy($id)
    {
        if (is_null($command_schedule = CommandSchedule::find($id)))
            throw new ResourseNotFoundException('front.command_schedule');

        $this->checkException(CommandSchedule::class, 'own', $command_schedule);

        if ($command_schedule->schedule)
            $command_schedule->schedule->delete();
        
        $command_schedule->delete();

        return ['status' => 1];
    }

    public function logs($id)
    {
        $command_schedule = CommandSchedule::find($id);

        if (is_null($command_schedule))
            throw new ResourseNotFoundException('front.command_schedule');

        $this->checkException(CommandSchedule::class, 'own', $command_schedule);

        return view('front::SendCommand.schedule.' . (request()->filled('page') ? 'logs_table' : 'logs'), [
            'logs'             => $command_schedule->sentCommands()->latest()->paginate(15),
            'command_schedule' => $command_schedule,
        ]);
    }

    private function createGprs($data)
    {
        SendCommandGprsFormValidator::validate('create', $data);

        $command_schedule = CommandSchedule::create([
            'user_id'    => $this->user->id,
            'connection' => SendCommandService::CONNECTION_GPRS,
            'command'    => $data['type'],
            'parameters' => $this->getGprsParameters($data),
        ]);

        return $command_schedule;
    }

    private function createSms($data)
    {
        SendCommandFormValidator::validate('create', $data);

        return CommandSchedule::create([
            'user_id'    => $this->user->id,
            'connection' => SendCommandService::CONNECTION_SMS,
            'command'    => 'custom',
            'parameters' => Arr::only($data, 'message'),
        ]);
    }

    private function updateGprs($command_schedule, $data)
    {
        SendCommandGprsFormValidator::validate('create', $data);

        $command_schedule->update([
            'connection' => SendCommandService::CONNECTION_GPRS,
            'command'    => request('type'),
            'parameters' => $this->getGprsParameters($data),
        ]);
    }

    private function updateSms($command_schedule, $data)
    {
        $data['message'] = Arr::get($data, 'message_sms');

        SendCommandFormValidator::validate('create', $data);

        $command_schedule->update([
            'connection' => SendCommandService::CONNECTION_SMS,
            'command'    => 'custom',
            'parameters' => Arr::only($data, 'message'),
        ]);
    }

    private function getGprsParameters($data)
    {
        if (Str::startsWith($data['type'], 'template_'))
            list($data['type'], $data['gprs_template_id']) = explode('_', $data['type']);

        $command = (new Commands())->get($data['type']);

        $parameters = [];

        if (isset($command['attributes']))
            $parameters = $command['attributes']->map(function ($attribute) {
                return $attribute->getName();
            })->all();

        if ($data['type'] == 'template')
            $parameters[] = 'data';

        $keys = array_intersect(array_keys($data), [
            Commands::KEY_DATA,
            Commands::KEY_INDEX,
            Commands::KEY_DEVICE_PASSWORD,
            Commands::KEY_ENABLE,
            Commands::KEY_FREQUENCY,
            Commands::KEY_MESSAGE,
            Commands::KEY_PHONE,
            Commands::KEY_PORT,
            Commands::KEY_RADIUS,
            Commands::KEY_TIMEZONE,
            Commands::KEY_UNIT,
        ]);

        if ($keys)
            $parameters = array_merge($parameters, $keys);

        return empty($parameters) ? null : Arr::only($data, $parameters) ;
    }

    private function isCommandGprs($connection)
    {
        return $connection == SendCommandService::CONNECTION_GPRS;
    }

    private function validDeviceIds($device_ids)
    {
        $devices = Device::findMany($device_ids);

        foreach ($devices as $device)
        {
            if ( ! $this->user->own($device))
                unset($device_ids[$device->id]);
        }

        if (empty($device_ids))
            throw new AuthorizationException();

        return $device_ids;
    }
}
