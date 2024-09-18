<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class CityeasyProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_POSITION_SINGLE),
            $this->initCommand(Commands::TYPE_POSITION_PERIODIC),
            $this->initCommand(Commands::TYPE_POSITION_STOP),
            $this->initCommand(Commands::TYPE_SET_TIMEZONE),
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }
}