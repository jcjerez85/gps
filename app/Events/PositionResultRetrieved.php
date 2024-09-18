<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Device;

class PositionResultRetrieved
{
    use Dispatchable, SerializesModels;

    public $device;
    public $result;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Device $device, string $result)
    {
        $this->result = $result;
        $this->device = $device;
    }
}
