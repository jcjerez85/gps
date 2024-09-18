<?php

namespace Tobuli\Helpers\Alerts;

use Tobuli\Entities\Device;
use Tobuli\Entities\Event;

class IdleDurationAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->check())
            return null;

        $event = $this->getEvent();

        $event->type = Event::TYPE_IDLE_DURATION;
        $event->message = '';

        $event->setAdditional('idle_duration', round($this->device->getIdleDuration() / 60));

        $this->silent($event);

        return [$event];
    }

    public function check()
    {
        if ( $this->alert->idle_duration < 1 )
            return false;

        $duration = round($this->device->getIdleDuration() / 60);

        if ($duration < $this->alert->idle_duration )
            return false;

        if ($this->device->isOffline())
            return false;

        $position = $this->getPosition();

        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        $check_at = max($this->device->traccar->moved_at, $this->device->traccar->engine_off_at);

        if ( ! $this->checkOccurred($check_at))
            return false;

        return true;
    }
}