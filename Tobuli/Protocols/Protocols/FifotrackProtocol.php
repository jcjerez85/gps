<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class FifotrackProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_CUSTOM),
            $this->initCommand(Commands::TYPE_REQUEST_PHOTO),
        ];
    }
}