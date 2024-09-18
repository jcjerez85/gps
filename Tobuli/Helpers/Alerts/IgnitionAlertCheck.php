<?php

namespace Tobuli\Helpers\Alerts;


use Tobuli\Entities\DeviceSensor;
use Tobuli\Entities\Event;

class IgnitionAlertCheck extends AlertCheck
{
    /**
     * @var DeviceSensor
     */
    protected $sensor;

    public function checkEvents($position, $prevPosition)
    {
        $this->sensor = $this->device->getEngineSensor();

        if (is_null($this->sensor))
            return null;

        $currentStatus = $this->check($position);

        switch ((int)$this->alert->state) {
            // only ON
            case 1:
                if ($currentStatus !== true) return null;
                break;
            // only OFF
            case 2:
                if ($currentStatus !== false) return null;
                break;
        }

        $previousStatus = $this->check($prevPosition);
        $offline_timeout = settings('main_settings.default_object_online_timeout') * 60;

        //device was offline, prev engine status unknown
        if ($prevPosition && (strtotime($position->time) - strtotime($prevPosition->time) > $offline_timeout))
            $previousStatus = null;

        if (is_null($currentStatus))
            return null;

        if ($currentStatus == $previousStatus)
            return null;

        $event = $this->getEvent();

        $event->type = $currentStatus ? Event::TYPE_IGNITION_ON : Event::TYPE_IGNITION_OFF;
        $event->message = '';

        return [$event];
    }

    protected function check($position)
    {
        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        return $this->getEngineStatus($position);
    }

    protected function getEngineStatus($position) {
        $status = $this->sensor->getValue($position->other, false);

        if (is_null($status))
            return null;

        if  ($status)
            return true;

        return false;
    }

}