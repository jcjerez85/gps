<?php

namespace Tobuli\Protocols\Protocols;

use CustomFacades\Field;
use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class MeitrackProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_ENGINE_STOP),
            $this->initCommand(Commands::TYPE_ENGINE_RESUME),
            $this->initCommand(Commands::TYPE_ALARM_ARM),
            $this->initCommand(Commands::TYPE_ALARM_DISARM),
            $this->initCommand(Commands::TYPE_POSITION_SINGLE),
            $this->initCommand(Commands::TYPE_POSITION_LOG),
            $this->initCommand(Commands::TYPE_REQUEST_PHOTO, [
                Field::select(Commands::KEY_INDEX, Commands::KEY_INDEX, 1)
                    ->setOptions([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                    ])
                    ->setRequired()
            ]),
            $this->initCommand(Commands::TYPE_SEND_SMS),
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }
}