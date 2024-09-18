<?php

namespace Tobuli\Helpers\Alerts;


use Tobuli\Entities\Event;

class SosAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->check($position))
            return null;

        $event = $this->getEvent();

        $event->type = Event::TYPE_SOS;
        $event->message = 'SOS';

        $this->silent($event);

        return [$event];
    }

    protected function check($position)
    {
        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        if ($position->getParameter('alarm') != 'sos')
            return false;

        return true;
    }
}