<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class PretraceProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_POSITION_PERIODIC),
            $this->initCommand(Commands::TYPE_CUSTOM),
        ];
    }
}
