<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class SuntechProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_ENGINE_STOP),
            $this->initCommand(Commands::TYPE_ENGINE_RESUME),
            $this->initCommand(Commands::TYPE_ALARM_ARM),
            $this->initCommand(Commands::TYPE_ALARM_DISARM),
            $this->initCommand(Commands::TYPE_POSITION_SINGLE),
            $this->initCommand(Commands::TYPE_REBOOT_DEVICE),
            //$this->initCommand(Commands::TYPE_OUTPUT_CONTROL),
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }
}