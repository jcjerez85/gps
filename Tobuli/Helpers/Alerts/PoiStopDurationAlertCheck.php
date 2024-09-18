<?php

namespace Tobuli\Helpers\Alerts;


use Tobuli\Entities\Event;

class PoiStopDurationAlertCheck extends AlertCheck
{
    public function checkEvents($position, $prevPosition)
    {
        if ( ! $this->checkDuration())
            return null;

        if ( ! $this->checkPosition())
            return null;

        //convert from m to km
        $tolerance = $this->alert->getDistanceTolerance() / 1000;
        $position  = $this->getPosition();

        $events = [];

        foreach ($this->alert->pois as $poi)
        {
            if ( ! $poi->pointIn($position, $tolerance))
                continue;

            if ($this->checkAnnounced($poi->id))
                continue;

            $event = $this->getEvent();

            $event->type = Event::TYPE_POI_STOP_DURATION;
            $event->poi_id = $poi->id;
            $event->setAdditional('poi', $poi->name);
            $event->setAdditional('distance', $poi->pointDistance($position));
            $event->setAdditional('stop_duration', round($this->device->getStopDuration() / 60));

            $this->silent($event);

            $events[] = $event;
        }

        return $events;
    }

    protected function checkDuration()
    {
        if ( $this->alert->stop_duration < 1 )
            return false;

        $stopDuration = round($this->device->getStopDuration() / 60);

        if ($stopDuration < $this->alert->stop_duration )
            return false;

        if ( ! $this->device->traccar->moved_at )
            return false;

        return true;
    }

    protected function checkPosition()
    {
        $position = $this->getPosition();

        if ( ! $position)
            return false;

        if ( ! $this->checkAlertPosition($position))
            return false;

        return true;
    }

    protected function checkAnnounced($poi_id)
    {
        if (Event::where('user_id', $this->alert->user_id)
            ->where('alert_id', $this->alert->id)
            ->where('device_id', $this->device->id)
            ->where('poi_id', $poi_id)
            ->where('type', Event::TYPE_POI_STOP_DURATION)
            ->where('created_at', '>=', $this->device->traccar->moved_at)
            ->first(['id']))
            return true;

        return false;
    }
}