<?php

namespace Tobuli\Helpers\Alerts;


use Tobuli\Entities\Event;

class OverspeedAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if (empty($this->alert->getOverspeed()))
            return null;

        if ( ! $position->isValid())
            return null;

        if ( ! $this->check($position))
            return null;

        if ($this->check($prevPosition))
            return null;

        $event = $this->getEvent();

        $event->type = Event::TYPE_OVERSPEED;
        $event->message = '';
        $event->setAdditional('overspeed_speed', $this->alert->getOverspeed());

        $this->silent($event);

        return [$event];
    }

    protected function check($position)
    {
        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        if (round($this->device->getSpeed($position)) <= round($this->alert->getOverspeed()))
            return false;

        return true;
    }
}