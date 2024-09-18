<?php namespace Tobuli\Services\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tobuli\Helpers\Tracker;
use Tobuli\Protocols\Commands;
use Tobuli\Protocols\Manager as ProtocolsManager;

class SendCommandService
{
    const CONNECTION_GPRS = 'gprs';
    const CONNECTION_SMS = 'sms';

    private $tracker;
    private $protocolManager;
    private $actor;
    private $user;

    public function __construct($actor = null)
    {
        $this->protocolManager = new ProtocolsManager();
        $this->tracker = new Tracker();
        $this->actor = $actor;
    }

    public function gprs($devices, $data, $user)
    {
        return $this->sendCommand(self::CONNECTION_GPRS, compact('devices', 'data', 'user'));
    }

    public function sms($devices, $message, $user)
    {
        $data = ['message' => $message, 'type' => 'custom'];

        return $this->sendCommand(self::CONNECTION_SMS, compact('devices', 'data', 'user'));
    }

    public function setActor($actor)
    {
        $this->actor = $actor;
    }

    private function sendCommand($connection, $arguments)
    {
        $arguments['data']['connection'] = $connection;
        $this->actor = $this->actor ?: $arguments['user'];
        $this->user = $arguments['user'];

        if ( ! ($arguments['devices'] instanceof Collection || is_array($arguments['devices'])))
            $arguments['devices'] = [$arguments['devices']];

        $responses = new Collection();

        foreach ($arguments['devices'] as $device) {
            $response = $this->{"_$connection"}($device, $arguments['data']);

            $response['device'] = $device->name;

            $responses->push($response);
        }

        return $responses;
    }

    private function _sms($device, $data)
    {
        if ( ! $this->user)
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.user_not_found')]);

        if ( ! $this->user->can('show', $device))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.dont_have_permission')]);

        $message = $this->prepareSmsMessage($device, $data['message']);

        $result = sendSMS($device->sim_number, $message, $this->user);

        $this->logSending($device, $data, [
            'parameters' => ['message' => $message],
            'status'     => $result['status'],
        ]);

        return $result;
    }

    private function _gprs($device, $data)
    {
        if (Str::startsWith($data['type'], 'template_'))
            list($data['type'], $data['gprs_template_id']) = explode('_', $data['type']);

        if ( ! $this->user)
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.user_not_found')]);

        if ( ! $this->user->can('show', $device))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.dont_have_permission')]);

        if ($data['type'] == 'custom' && ! $this->user->perm('send_command', 'edit'))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.dont_have_permission')]);

        if ( ! $device->isConnected())
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.no_gprs_connection')]);

        if ($device->gprs_templates_only && ! Str::startsWith($data['type'], 'template'))
            return $this->handleError($device, $data, ['status' => 0, 'error' => trans('front.no_templates')]);

        if ($error = $this->checkSpeedLimit($device, $data))
            return $this->handleError($device, $data, ['status' => 0, 'error' => $error]);

        $command = $this->protocolManager->protocol($device->protocol)->buildCommand($device, $data);

        $results = $this->tracker->sendCommand($command);

        $this->logSending($device, $data, [
            'status'     => $results['status'],
            'parameters' => Arr::get($command, 'attributes'),
            'response'   => Arr::get($results, 'message'),
        ]);

        if ($results['status'] == 0)
            $results['error'] = $results['message'];

        return $results;
    }

    private function handleError($device, $data, $results)
    {
        $this->logSending($device, $data, [
            'parameters' => null,
            'response'   => $results['error'],
            'status'     => $results['status'],
        ]);

        return $results;
    }

    private function logSending($device, $data, $additional)
    {
        $this->actor->sentCommands()->create([
                'user_id'     => $this->user->id,
                'device_imei' => $device->imei,
                'template_id' => empty($data['gprs_template_id']) ? null : $data['gprs_template_id'],
                'connection'  => $data['connection'],
                'command'     => $data['type'],
            ] + $additional);
    }

    private function prepareSmsMessage($device, $message)
    {
        return strtr($message, [
            '[%IMEI%]' => $device->imei
        ]);
    }

    private function checkSpeedLimit($device, $data)
    {
        $plugin = settings('plugins.send_command_speed_limit');

        if (!Arr::get($plugin, 'status')) {
            return null;
        }

        if ($device->getSpeed() < Arr::get($plugin, 'options.speed_limit')) {
            return null;
        }

        $messages = explode(';', Arr::get($plugin, 'options.messages'));
        $commands = Arr::get($plugin, 'options.commands');

        $containCommand = in_array($data['type'], $commands);
        $containMessage = !empty($data[Commands::KEY_DATA]) && in_array($data[Commands::KEY_DATA], $messages);

        if (!($containCommand || $containMessage)) {
            return null;
        }

        return trans('front.send_command_speed_limit_fail');
    }

}