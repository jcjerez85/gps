<?php

namespace Tobuli\Helpers\Alerts;


use App\Jobs\ConfirmUnpluggedAlert;
use Tobuli\Entities\DeviceSensor;
use Tobuli\Entities\Event;

class UnpluggedAlertCheck extends AlertCheck
{
    protected $sensor = null;

    public function checkEvents($position, $prevPosition)
    {
        $this->sensor = $this->getSensor();

        if (!$this->sensor)
            return null;

        $currentStatus = $this->check($position);

        if ($currentStatus)
            return null;

        if (is_null($currentStatus))
            return null;

        $previousStatus = $this->check($prevPosition);

        if (is_null($previousStatus))
            return null;

        if ($currentStatus === $previousStatus)
            return null;

        $event = $this->getEvent();
        $event->type = Event::TYPE_UNPLUGGED;
        $event->message = '';

        if (!$this->hasContinuousDuration())
            return [$event];

        $delay = $this->getContinuousDuration();

        dispatch(
            (new ConfirmUnpluggedAlert($event->toArray(), $delay))->delay($delay)
        );

        return [];
    }

    /**
     * @param $position
     * @return bool|null
     */
    protected function check($position)
    {
        if ( ! $position)
            return null;

        if ( ! $this->checkAlertPosition($position))
            return null;

        $value = $this->sensor->getValue($position->other, false);

        if (is_null($value))
            return null;

        return $value;
    }

    /**
     * @return DeviceSensor|null
     */
    protected function getSensor()
    {
        return $this->device->getSensorByType('plugged');
    }
}