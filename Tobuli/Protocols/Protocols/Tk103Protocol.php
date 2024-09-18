<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class Tk103Protocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_ENGINE_STOP),
            $this->initCommand(Commands::TYPE_ENGINE_RESUME),
            $this->initCommand(Commands::TYPE_POSITION_SINGLE),
            $this->initCommand(Commands::TYPE_POSITION_PERIODIC),
            $this->initCommand(Commands::TYPE_POSITION_STOP),
            $this->initCommand(Commands::TYPE_REBOOT_DEVICE),
            //$this->initCommand(Commands::TYPE_GET_VERSION),
            //$this->initCommand(Commands::TYPE_SET_ODOMETER),
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }
}