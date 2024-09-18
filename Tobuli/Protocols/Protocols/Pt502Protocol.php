<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class Pt502Protocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_ENGINE_STOP),
            $this->initCommand(Commands::TYPE_ENGINE_RESUME),
            $this->initCommand(Commands::TYPE_DOOR_OPEN),
            $this->initCommand(Commands::TYPE_DOOR_CLOSE),
            $this->initCommand(Commands::TYPE_OUTPUT_CONTROL),
            $this->initCommand(Commands::TYPE_ALARM_SPEED),
            $this->initCommand(Commands::TYPE_SET_TIMEZONE),
            $this->initCommand(Commands::TYPE_REQUEST_PHOTO),
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }

    protected function buildCommandEngineStop($device, $data)
    {
        $data[Commands::KEY_TYPE]  = Commands::TYPE_OUTPUT_CONTROL;
        $data[Commands::KEY_INDEX] = 1;
        $data[Commands::KEY_DATA]  = 1;

        return $data;
    }

    protected function buildCommandEngineResume($device, $data)
    {
        $data[Commands::KEY_TYPE]  = Commands::TYPE_OUTPUT_CONTROL;
        $data[Commands::KEY_INDEX] = 1;
        $data[Commands::KEY_DATA]  = 0;

        return $data;
    }

    protected function buildCommandDoorOpen($device, $data)
    {
        $data[Commands::KEY_TYPE]  = Commands::TYPE_OUTPUT_CONTROL;
        $data[Commands::KEY_INDEX] = 2;
        $data[Commands::KEY_DATA]  = 0;

        return $data;
    }

    protected function buildCommandDoorClose($device, $data)
    {
        $data[Commands::KEY_TYPE]  = Commands::TYPE_OUTPUT_CONTROL;
        $data[Commands::KEY_INDEX] = 2;
        $data[Commands::KEY_DATA]  = 1;

        return $data;
    }
}