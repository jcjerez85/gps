<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class DualcamProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_REQUEST_PHOTO),
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }

    protected function buildCommandrequestPhoto($device, $data)
    {
        $data[Commands::KEY_TYPE] = Commands::TYPE_CUSTOM;
        $data[Commands::KEY_DATA] = 'camreq:1,3';

        return $data;
    }
}
