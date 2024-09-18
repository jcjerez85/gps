<?php

namespace App\Events\Device;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Device;

class DeviceEvent extends Event implements DeviceEventInterface
{
    use SerializesModels;

    /**
     * @var Device
     */
    public $device;

    public function __construct(Device $device) {
        $this->device = $device;
    }
}
