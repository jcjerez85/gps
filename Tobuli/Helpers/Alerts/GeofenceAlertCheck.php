<?php

namespace Tobuli\Helpers\Alerts;

use Tobuli\Entities\Alert;
use Tobuli\Entities\Device;
use Tobuli\Entities\Event;

class GeofenceAlertCheck extends AlertCheck
{
    protected $movingGeofence;

    public function __construct(Device $device, Alert $alert)
    {
        parent::__construct($device, $alert);

        $this->movingGeofence = settings('plugins.moving_geofence.status');
    }

    public function checkEvents($position, $prevPosition)
    {
        if ( ! $position)
            return null;

        if ( ! $position->isValid())
            return null;

        $prevPosition = $this->device->positionTraccar();

        if ( ! $this->preCheck($position, $prevPosition))
            return null;

        $events = [];

        foreach ($this->alert->geofences as $geofence)
        {
            if ( ! $type = $this->check($position, $prevPosition, $geofence))
                continue;

            $event = $this->getEvent();

            $type = $type == 'geofence_in' ? Event::TYPE_ZONE_IN : Event::TYPE_ZONE_OUT;

            $event->type = $type;
            $event->message = $type;
            $event->geofence_id = $geofence->id;
            $event->setAdditional('geofence', $geofence->name);

            $this->silent($event);

            $events[] = $event;
        }

        return $events;
    }

    protected function check($position, $prevPosition, $geofence)
    {
        if ($this->geofenceMovesWithDevice($geofence)) {
            return false;
        }

        switch ($this->alert->type) {
            case 'geofence_in':
                if ( ! $this->checkGeofenceWithSchedules($position, $geofence))
                    return false;
                if ($this->checkGeofenceWithSchedules($prevPosition, $geofence))
                    return false;

                return 'geofence_in';

            case 'geofence_out':
                //is current in
                if ($this->checkGeofence($position, $geofence))
                    return false;

                //is previous in
                if ( ! $this->checkGeofence($prevPosition, $geofence))
                    return false;

                return 'geofence_out';

            case 'geofence_inout':
                $isCurrentIn = $this->checkGeofence($position, $geofence);
                $isPreviousIn = $this->checkGeofence($prevPosition, $geofence);

                if ($isCurrentIn && ! $isPreviousIn)
                    return 'geofence_in';
                if (! $isCurrentIn && $isPreviousIn)
                    return 'geofence_out';

                return false;

            default:
                return false;
        }
    }

    protected function checkGeofence($position, $geofence)
    {
        if ( ! $position)
            return false;

        if ( ! $position->isValid())
            return false;

        return $geofence->pointIn($position);
    }

    protected function checkGeofenceWithSchedules($position, $geofence)
    {
        if ( ! $this->checkSchedules($position->time))
            return false;

        return $this->checkGeofence($position, $geofence);
    }

    protected function isPointEqual($position, $prevPosition)
    {
        if ( ! $position)
            return false;

        if ( ! $prevPosition)
            return false;

        if ($position->latitude != $prevPosition->latitude)
            return false;

        if ($position->longitude != $prevPosition->longitude)
            return false;

        return true;
    }

    protected function preCheck($position, $prevPosition)
    {
        if ($this->isPointEqual($position, $prevPosition))
            return false;

        switch ($this->alert->type) {
            case 'geofence_out':
            case 'geofence_inout':
                if ( ! $this->checkSchedules($position->time))
                    return false;
                break;
        }

        return true;
    }

    protected function geofenceMovesWithDevice($geofence)
    {
        if (! $this->movingGeofence) {
            return false;
        }

        return $geofence->device_id == $this->device->id;
    }
}