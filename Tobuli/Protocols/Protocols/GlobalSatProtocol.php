<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class GlobalSatProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_OUTPUT_CONTROL),
            $this->initCommand(Commands::TYPE_CUSTOM),
        ];
    }
}
