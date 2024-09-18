<?php

namespace Tobuli\Protocols\Protocols;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tobuli\Entities\CommandTemplate;
use Tobuli\Entities\UserGprsTemplate;
use Tobuli\Entities\UserSmsTemplate;
use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;
use CustomFacades\Field;
use Tobuli\Services\Commands\SendCommandService;

class BaseProtocol implements Protocol
{
    protected $passwordRequired = false;

    protected $commandsManager;

    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }

    public function getCommands()
    {
        $commands = $this->commands();

        if ($this->passwordRequired) {
            $commands = $this->appendPasswordAttribute($commands);
        }

        return $commands;
    }

    public function getTemplateCommands($templates, $display = true)
    {
        $commands = [];

        if ( ! $templates)
            return $commands;

        foreach ($templates as $template) {
            if ($template instanceof CommandTemplate && $template->type == SendCommandService::CONNECTION_GPRS)
                $commands[] = $this->initGprsTemplateCommand($template, $display);
            if ($template instanceof CommandTemplate && $template->type == SendCommandService::CONNECTION_SMS)
                $commands[] = $this->initSmsTemplateCommand($template, $display);
        }

        return $commands;
    }

    public function initSmsTemplateCommand(CommandTemplate $template, $display)
    {
        $command = [
            'type'  => 'template_' . $template->id,
            'title' => $template->title . '(' . trans('validation.attributes.sms_template_id') . ')',
            'connection' => SendCommandService::CONNECTION_SMS
        ];

        if ($display)
            $command['attributes'] = collect([
                Field::text('message', trans('validation.attributes.message'), $template->message)
            ]);

        return $command;
    }

    public function initGprsTemplateCommand(CommandTemplate $template, $display)
    {
        $command = [
            'type'  => 'template_' . $template->id,
            'title' => $template->title . '(' . trans('validation.attributes.gprs_template_id') . ')',
            'connection' => SendCommandService::CONNECTION_GPRS
        ];

        if ($display)
            $command['attributes'] = collect([
                Field::text(Commands::KEY_DATA, trans('validation.attributes.message'), $template->message)
                    ->setDescription(
                        trans('front.raw_command_supports')
                        . '<br><br>'
                        . trans('front.gprs_template_variables')
                    ),
            ]);

        return $command;
    }

    protected function initCommand($type, $attributes = [])
    {
        if ( ! $this->commandsManager)
            $this->commandsManager = new Commands();

        return $this->commandsManager->get($type, $attributes);
    }

    protected function appendPasswordAttribute($commands)
    {
        foreach ($commands as &$command)
        {
            if (empty($command['attributes'])) {
                $command['attributes'] = collect([]);
            }

            $command['attributes']->push(
                Field::string(Commands::KEY_DEVICE_PASSWORD, trans('validation.attributes.password'))
            );
        }

        return $commands;
    }

    public function buildCommand($device, $data)
    {
        $data = $this->_buildCommand($device, $data);

        $attributes = Arr::only($data, [
            Commands::KEY_FREQUENCY,
            Commands::KEY_TIMEZONE,
            Commands::KEY_DEVICE_PASSWORD,
            Commands::KEY_RADIUS,
            Commands::KEY_MESSAGE,
            Commands::KEY_ENABLE,
            Commands::KEY_DATA,
            Commands::KEY_INDEX,
            Commands::KEY_PHONE,
            Commands::KEY_SERVER,
            Commands::KEY_PORT
        ]);

        foreach ($attributes as $key => $value) {
            switch ($key) {
                case Commands::KEY_FREQUENCY:
                case Commands::KEY_RADIUS:
                case Commands::KEY_INDEX:
                case Commands::KEY_PORT:
                    $attributes[$key] = (int)$value;
                    break;

                case Commands::KEY_ENABLE:
                    $attributes[$key] = (boolean)$value;
                    break;
            }
        }

        $data = [
            'uniqueId' => $device->imei,
            'type' => $data['type'],
        ];

        if ( ! empty($attributes))
            $data['attributes'] = $attributes;

        return $data;
    }

    protected function _buildCommand($device, $data)
    {
        if (Str::startsWith($data['type'], 'template_'))
            list($data['type'], $data['gprs_template_id']) = explode('_', $data['type']);

        $method = 'buildCommand' . ucfirst($data['type']);

        if (method_exists($this,$method))
            $data = call_user_func([$this,$method], $device, $data);

        return $data;
    }

    protected function buildCommandPositionPeriodic($device, $data)
    {
        if (empty($data['unit']))
            return $data;

        switch ($data['unit'])
        {
            case 'minute':
                $data['frequency'] *= 60;
                break;
            case 'hour':
                $data['frequency'] *= 3600;
                break;
        }

        return $data;
    }

    protected function buildCommandPositionLog($device, $data)
    {
        if (empty($data['unit']))
            return $data;

        switch ($data['unit'])
        {
            case 'minute':
                $data['frequency'] *= 60;
                break;
            case 'hour':
                $data['frequency'] *= 3600;
                break;
        }

        return $data;
    }

    protected function buildCommandCustom($device, $data)
    {
        $imei = $device->imei;

        if ($device->protocol == 'tk103') {
            $imei = '0' . substr($imei, -11);
        }

        $command = strtr($data[Commands::KEY_DATA], [
            '[%IMEI%]' => $imei
        ]);

        $data[Commands::KEY_DATA] = $command;

        return $data;
    }

    protected function buildCommandTemplate($device, $data)
    {
        $user = getActingUser();

        $cacheKey = 'grps_template_build_' . ($user->id ?? 0) . '_' . $data['gprs_template_id'];

        $grps_template = Cache::store('array')->rememberForever($cacheKey, function() use ($user, $data) {
            return UserGprsTemplate::userAccessible($user)->find($data['gprs_template_id']);
        });

        $message = $grps_template ? $grps_template->message : '';

        $canEdit = $user->perm('send_command', 'edit') && !$device->gprs_templates_only;

        if ( $canEdit && isset($data[Commands::KEY_DATA]))
            $message = $data[Commands::KEY_DATA];

        $data[Commands::KEY_TYPE] = Commands::TYPE_CUSTOM;
        $data[Commands::KEY_DATA] = $message;

        return $this->buildCommandCustom($device, $data);
    }
}