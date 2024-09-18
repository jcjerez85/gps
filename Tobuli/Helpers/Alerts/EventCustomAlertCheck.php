<?php

namespace Tobuli\Helpers\Alerts;

use Tobuli\Entities\Event;

class EventCustomAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->checkAlertPosition($position)) {
            if ($this->hasContinuousDuration()) {
                $this->resetStartedAt();
            }

            return null;
        }

        $events = [];

        foreach ($this->alert->events_custom as $eventCustom)
        {
            $currentStatus = $this->check($position, $eventCustom);

            if ($this->hasContinuousDuration()) {
                if ( ! is_null($currentStatus))
                    $currentStatus ? $this->setStartedAt($position->time) : $this->resetStartedAt();

                if ( ! $currentStatus)
                    continue;

                if ( ! $this->checkContinuousDuration($position))
                    continue;

                if (!$eventCustom->always && $this->checkContinuousDuration($prevPosition))
                    continue;

            } else {
                if ( ! $currentStatus)
                    continue;

                if (!$eventCustom->always && $this->check($prevPosition, $eventCustom))
                    continue;
            }

            $event = $this->getEvent();

            $event->type = Event::TYPE_CUSTOM;
            $event->message = $eventCustom->message;

            $this->silent($event);

            $events[] = $event;
        }

        return $events;
    }

    protected function check($position, $eventCustom)
    {
        if ( ! $position)
            return null;

        if ($eventCustom->protocol != $position->protocol)
            return null;

        if ( ! $this->checkCustomEventConditions($position, $eventCustom))
            return false;

        return true;
    }

    protected function checkCustomEventConditions($position, $customEvent)
    {
        $parameters = $position->parameters;
        $parameters['speed'] = $this->device->getSpeed($position);

        if (empty($customEvent->conditions))
            return false;

        foreach ($customEvent->conditions as $condition)
        {
            if ( ! array_key_exists($condition['tag'], $parameters))
                return false;

            $value = $parameters[$condition['tag']];

            if ($condition['tag'] == 'rfid' && $position->protocol == 'meitrack')
                $value = hexdec($value);

            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $condition['tag_value'], $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $condition['tag_value'] = $match['3'];
                $value = substr($value, $match['1'], $match['2']);
            }

            if ( ! checkCondition($condition['type'], $value, $condition['tag_value']))
            {
                return false;
            }
        }

        return true;
    }


    protected function checkContinuousDuration($position)
    {
        if (empty($position))
            return null;

        if (empty($this->alert->pivot->started_at))
            return false;

        $duration = strtotime($position->time) - strtotime($this->alert->pivot->started_at);

        return $this->alert->continuous_duration < $duration;
    }

    protected function hasContinuousDuration()
    {
        return $this->alert->continuous_duration > 0 ? true : false;
    }

    protected function setStartedAt($time)
    {
        if (!is_null($this->alert->pivot->started_at))
            return;

        $this->saveStartedAt($time);
    }

    protected function resetStartedAt()
    {
        if (is_null($this->alert->pivot->started_at))
            return;

        $this->saveStartedAt(null);
    }

    protected function saveStartedAt($time)
    {
        $this->alert->pivot->started_at = $time;

        $this->alert->devices()->updateExistingPivot($this->device->id, [
            'started_at' => $time
        ]);
    }
}