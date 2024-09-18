<?php

namespace Tobuli\Helpers\Alerts;


use Tobuli\Entities\Event;

class BaseAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->check())
            return null;

        $event = $this->getEvent();
        $this->silent($event);

        return [$event];
    }

    public function check()
    {
        $position = $this->getPosition();

        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        return true;
    }
}