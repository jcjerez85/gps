<?php

namespace Tobuli\Helpers\Alerts;

use Tobuli\Entities\Event;

class DriverAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->checkAlertPosition($position))
            return null;

        $rfids = $this->getRFIDs($position);

        if (!$rfids)
            return null;

        $events = [];

        foreach ($this->alert->drivers as $driver)
        {
            if ( ! $this->check($position, $driver, $rfids))
                continue;

            $event = $this->getEvent();

            $event->type = Event::TYPE_DRIVER;
            $event->message = $driver->name;
            $event->setAdditional('driver_id', $driver->id);
            $event->setAdditional('driver_name', $driver->name);

            $this->silent($event);

            $events[] = $event;
        }

        return $events;
    }

    protected function check($position, $driver, $rfids)
    {
        if ($this->device->current_driver_id == $driver->id)
            return false;

        foreach ($rfids as $rfid) {
            if ($rfid == $driver->rfid)
                return true;
        }

        return false;
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