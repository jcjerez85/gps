<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class EelinkProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_POSITION_SINGLE),
            $this->initCommand(Commands::TYPE_ENGINE_STOP),
            $this->initCommand(Commands::TYPE_ENGINE_RESUME),
            $this->initCommand(Commands::TYPE_REBOOT_DEVICE),
            $this->initCommand(Commands::TYPE_CUSTOM),
        ];
    }
}
