<?php

namespace Tobuli\Protocols\Protocols;

use Formatter;
use Carbon\Carbon;
use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;
use CustomFacades\Field;

class TeltonikaProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        $commands = [
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];

        if (settings('dualcam.enabled')) {
            $commands[] = $this->initCommand(Commands::TYPE_REQUEST_PHOTO, [
                $this->getIndexAttribute()
            ]);

            $commands[] = $this->initCommand(Commands::TYPE_REQUEST_VIDEO, [
                Field::datetime(
                    'datetime', trans('validation.attributes.datetime'),
                    date('Y-m-d H:i:s', Formatter::time()->now())
                )->setRequired()
                    ,
                Field::number('duration', trans('validation.attributes.duration'), 5)
                    ->setRequired()
                    ->setMax(30)
                    ->addValidation('numeric')
                    ,
                $this->getIndexAttribute()
            ]);
        }

        return $commands;
    }

    protected function buildCommandrequestPhoto($device, $data)
    {
        $source = $data[Commands::KEY_INDEX] ?? 3;
        $data[Commands::KEY_TYPE] = Commands::TYPE_CUSTOM;
        $data[Commands::KEY_DATA] = "camreq:1,$source";

        return $data;
    }

    protected function buildCommandrequestVideo($device, $data)
    {
        $source = $data[Commands::KEY_INDEX];
        $timestamp = strtotime(Formatter::time()->reverse($data['datetime']));
        $duration = $data['duration'];

        $data = [];
        $data[Commands::KEY_TYPE] = Commands::TYPE_CUSTOM;
        $data[Commands::KEY_DATA] = "camreq:0,$source,$timestamp,$duration";

        return $data;
    }

    protected function getIndexAttribute()
    {
        return Field::select(Commands::KEY_INDEX, Commands::KEY_INDEX, 1)
            ->setRequired()
            ->setOptions([
                1 => trans('global.front'),
                2 => trans('global.rear'),
                3 => trans('global.both'),
            ])
            ;
    }
}