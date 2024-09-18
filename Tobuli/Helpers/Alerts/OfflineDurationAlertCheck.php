<?php

namespace Tobuli\Helpers\Alerts;


use Tobuli\Entities\Event;

class OfflineDurationAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->check())
            return null;

        $event = $this->getEvent();

        $event->type = Event::TYPE_OFFLINE_DURATION;
        $event->message = '';
        $event->setAdditional('offline_duration', $this->offlineDuration());

        $this->silent($event);

        return [$event];
    }

    public function check()
    {
        if ( $this->alert->offline_duration < 1 )
            return false;

        $offline_duration = $this->offlineDuration();

        if ( ! $offline_duration)
            return false;

        if ($offline_duration < $this->alert->offline_duration)
            return false;

        $position = $this->getPosition();

        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        if ( ! $this->checkOccurred($this->device->last_connect_time))
            return false;

        return true;
    }

    public function offlineDuration()
    {
        $last_connection = $this->device->last_connect_timestamp;

        if (empty($last_connection))
            return false;

        return round((time() - $last_connection) / 60);
    }
}