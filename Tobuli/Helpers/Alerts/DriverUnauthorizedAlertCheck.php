<?php

namespace Tobuli\Helpers\Alerts;

use Illuminate\Support\Facades\Cache;
use Tobuli\Entities\Event;
use Tobuli\Entities\UserDriver;

class DriverUnauthorizedAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->checkAlertPosition($position))
            return null;

        $rfid = $this->getRfids($position);

        if (empty($rfid))
            return null;

        $driver = $this->getDriver($rfid);

        if ($driver && $this->device->current_driver_id == $driver->id) {
            return null;
        }

        $authorized = $this->alert->authorized;

        if ($driver) {
            $canDrive = $driver->devices()->where('id', $this->device->id)->count();

            if ($canDrive xor $authorized)
                return null;

            $driver_name = $driver->name;
            $driver_id = $driver->id;
        } else {
            if ($authorized)
                return null;

            $driver_name = $rfid[0];
            $driver_id = null;
        }

        $event = $this->getEvent();

        $event->type = $authorized ? Event::TYPE_DRIVER_AUTHORIZED : Event::TYPE_DRIVER_UNAUTHORIZED;
        $event->message = $driver_name;
        $event->setAdditional('driver_id', $driver_id);
        $event->setAdditional('driver_name', $driver_name);

        $this->silent($event);

        return [$event];
    }

    protected function getDriver($rfid)
    {
        $key = 'user_driver.'.md5(json_encode($rfid));

        return Cache::store('array')->rememberForever($key, function() use ($rfid) {
            return UserDriver::whereIn('rfid', $rfid)->first();
        });
    }

    protected function getRFIDs($position)
    {
        if ( ! isset($this->rfid_sensor))
            $this->rfid_sensor = $this->device->getRfidSensor();

        if ($this->rfid_sensor) {
            $rfid = $this->rfid_sensor->getValue($position->other, false, null);
            return $rfid ? [$rfid] : null;
        }

        return $position->getRfids();
    }
}