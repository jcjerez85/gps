<?php

namespace Tobuli\Helpers\Alerts;

use Carbon\Carbon;
use Formatter;
use Tobuli\Entities\Event;

class DistanceAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        $distance = $this->check();

        if ( ! $distance)
            return null;

        $event = $this->getEvent();

        $event->type = Event::TYPE_DISTANCE;
        $event->message = '';
        $event->setAdditional('distance', $distance);
        $event->setAdditional('limit', $this->alert->distance);

        $this->silent($event);

        return [$event];
    }

    public function check()
    {
        if ( $this->alert->distance < 1 )
            return false;

        $position = $this->getPosition();

        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        Formatter::byUser($this->alert->user);

        $dateFrom = $this->calcDateFrom();

        if ( ! $this->checkOccurred($dateFrom))
            return false;

        $distance = $this->device->getDistanceBetween($dateFrom, date('Y-m-d H:i:s'));

        if ($distance < $this->alert->getDistance())
            return false;

        return $distance;
    }

    protected function calcDateFrom()
    {
        $period = $this->alert->period;

        if ($period < 1)
            return $this->alert->created_at;

        $createdAt = Carbon::make(Formatter::time()->reverse(
            $this->alert->created_at->startOfDay()
        ));

        $iterations = intval(Carbon::now()->diff($createdAt)->days / $period);

        return $createdAt->addDays($iterations * $period);
    }
}