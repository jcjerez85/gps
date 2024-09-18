<?php

namespace Tobuli\Helpers\Alerts;

use Tobuli\Entities\Device;
use Tobuli\Entities\Event;

class TimeDurationAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->check())
            return null;

        $event = $this->getEvent();

        $event->type = Event::TYPE_TIME_DURATION;
        $event->message = '';

        $event->setAdditional('time_duration', $this->alert->time_duration);

        $this->silent($event);

        return [$event];
    }

    public function check()
    {
        if ( $this->alert->time_duration < 1 )
            return false;

        if (!$this->checkOccurred(time() - $this->alert->time_duration * 60))
            return false;

        $position = $this->getPosition();

        if ( $position && ! $this->checkAlertPosition($position))
            return false;

        return true;
    }

    public function checkDuration()
    {
        $last = Event::where('user_id', $this->alert->user_id)
            ->where('alert_id', $this->alert->id)
            ->where('device_id', $this->device->id)
            ->where('type', Event::TYPE_TIME_DURATION)
            ->latest()
            ->first(['id', 'created_at']);

        if (empty($last))
            return true;

        return time() - strtotime($last->created_at) >= ($this->alert->time_duration * 60);
    }
}