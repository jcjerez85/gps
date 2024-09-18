<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class RuptelaProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_OUTPUT_CONTROL),
            //$this->initCommand(Commands::TYPE_SET_CONNECTION),
            //$this->initCommand(Commands::TYPE_SET_ODOMETER),
            $this->initCommand(Commands::TYPE_CUSTOM),
            $this->initCommand(Commands::TYPE_REQUEST_PHOTO),
        ];
    }
}