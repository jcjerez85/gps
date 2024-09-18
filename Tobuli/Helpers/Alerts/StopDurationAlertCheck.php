<?php

namespace Tobuli\Helpers\Alerts;


use Tobuli\Entities\Event;

class StopDurationAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->check())
            return null;

        $event = $this->getEvent();

        $event->type = Event::TYPE_STOP_DURATION;
        $event->message = '';
        $event->setAdditional('stop_duration', round($this->device->getStopDuration() / 60));
        $event->setAdditional('moved_at', $this->device->moved_at);

        $this->silent($event);

        return [$event];
    }

    public function check()
    {
        if ( $this->alert->stop_duration < 1 )
            return false;

        $stopDuration = $this->device->getStopDuration();

        if (is_null($stopDuration))
            return false;

        $stopDuration = round($stopDuration / 60);

        if ($stopDuration < $this->alert->stop_duration )
            return false;

        $moved_at = $this->device->traccar->moved_at;

        if ( ! $moved_at )
            return false;

        if ( ! $this->checkOccurred($moved_at))
            return false;

        $position = $this->getPosition();

        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        return true;
    }
}