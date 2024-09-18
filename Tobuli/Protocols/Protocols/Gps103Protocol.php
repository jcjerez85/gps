<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class Gps103Protocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_ENGINE_STOP),
            $this->initCommand(Commands::TYPE_ENGINE_RESUME),
            $this->initCommand(Commands::TYPE_ALARM_ARM),
            $this->initCommand(Commands::TYPE_ALARM_DISARM),
            $this->initCommand(Commands::TYPE_POSITION_SINGLE),
            $this->initCommand(Commands::TYPE_POSITION_PERIODIC),
            $this->initCommand(Commands::TYPE_POSITION_STOP),
            $this->initCommand(Commands::TYPE_REQUEST_PHOTO),
            $this->initCommand(Commands::TYPE_CUSTOM),
        ];
    }
}