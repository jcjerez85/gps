<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Device;

class DeviceEngineChanged extends Event
{
    use SerializesModels;

    public $device;

    public function __construct(Device $device) {
        $this->device = $device;
    }
}
