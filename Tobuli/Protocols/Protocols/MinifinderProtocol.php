<?php

namespace Tobuli\Protocols\Protocols;

use Tobuli\Protocols\Protocol;
use Tobuli\Protocols\Commands;

class MinifinderProtocol extends BaseProtocol implements Protocol
{
    protected function commands()
    {
        return [
            $this->initCommand(Commands::TYPE_SET_TIMEZONE),
            //$this->initCommand(Commands::TYPE_VOICE_MONITORING),
            $this->initCommand(Commands::TYPE_ALARM_SPEED),
            $this->initCommand(Commands::TYPE_ALARM_GEOFENCE),
            //$this->initCommand(Commands::TYPE_ALARM_VIBRATION),
            //$this->initCommand(Commands::TYPE_ALARM_FALL),
            //$this->initCommand(Commands::TYPE_SET_AGPS),
            //$this->initCommand(Commands::TYPE_SET_INDICATOR),
            //$this->initCommand(Commands::TYPE_MODE_POWER_SAVING),
            //$this->initCommand(Commands::TYPE_MODE_DEEP_SLEEP),
            $this->initCommand(Commands::TYPE_SOS_NUMBER),
            $this->initCommand(Commands::TYPE_CUSTOM)
        ];
    }
}