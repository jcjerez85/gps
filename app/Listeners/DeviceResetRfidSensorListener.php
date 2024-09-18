<?php

namespace App\Listeners;

use App\Events\DeviceEngineChanged;

class DeviceResetRfidSensorListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DeviceEngineChanged  $event
     * @return void
     */
    public function handle(DeviceEngineChanged $event)
    {
        if (! settings('plugins.device_driver_reset_engine.status')) {
            return;
        }

        if (false !== $event->device->getEngineStatus()) {
            return;
        }

        $sensors = $event->device->sensors()->where('type', 'rfid')->get();

        foreach ($sensors as $sensor) {
            $sensor->setValue(null);
            $sensor->save();
        }
    }
}
