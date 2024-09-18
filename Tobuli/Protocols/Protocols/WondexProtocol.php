<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class WondexProtocol extends BaseProtocol implements Protocol
{
    protected $passwordRequired = true;

    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_POSITION_SINGLE),
            $this->initCommand(Commands::TYPE_REBOOT_DEVICE),
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }
}