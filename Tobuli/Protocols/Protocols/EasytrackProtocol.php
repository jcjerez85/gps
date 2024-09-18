<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class EasytrackProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_ENGINE_STOP),
            $this->initCommand(Commands::TYPE_ENGINE_RESUME),
            $this->initCommand(Commands::TYPE_ALARM_ARM),
            $this->initCommand(Commands::TYPE_ALARM_DISARM),
            $this->initCommand(Commands::TYPE_CUSTOM),
        ];
    }
}