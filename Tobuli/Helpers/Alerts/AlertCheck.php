<?php

namespace Tobuli\Helpers\Alerts;


use Carbon\Carbon;
use Illuminate\Support\Arr;
use Tobuli\Entities\Alert;
use Tobuli\Entities\Device;
use Tobuli\Entities\Event;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Services\ScheduleService;


abstract class AlertCheck
{
    /**
     * @var Device
     */
    protected $device;

    /**
     * @var Alert
     */
    protected $alert;

    /**
     * @var ScheduleService
     */
    protected $scheduleService;

    /**
     * @var TraccarPosition
     */
    protected $position;

    /**
     * @var TraccarPosition
     */
    protected $prevPosition;

    protected $checkPrevious = false;
    protected $checkIsPrevious = false;
    protected $checkIsHistory = false;

    abstract public function checkEvents($position, $prevPosition);

    public function __construct(Device $device, Alert $alert)
    {
        $this->setDevice($device);
        $this->setAlert($alert);
    }

    public function setDevice(Device $device)
    {
        $this->device = $device;
    }

    public function setAlert(Alert $alert)
    {
        $this->alert = $alert;
        $this->scheduleService = new ScheduleService($alert->schedules ?? []);
    }

    public function setCurrentPosition($position)
    {
        $this->position = $position;
    }

    public function setPreviousPosition($position)
    {
        $this->prevPosition = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getEvents()
    {
        return $this->checkEvents($this->position, $this->prevPosition);
    }

    protected function checkAlertPosition($position)
    {
        if ( ! $this->checkSchedules($position->time))
            return false;

        if ( ! $this->checkZones($position->latitude, $position->longitude))
            return false;

        return true;
    }

    /**
     * @param string|int $time
     * @return bool
     */
    protected function checkActiveTime($time)
    {
        if ( ! is_integer($time))
            $time = strtotime($time);

        $alertPivotActiveFrom = $this->alert->pivot->active_from ?? null;
        $devicePivotActiveFrom = $this->device->pivot->active_from ?? null;
        $activeFrom = max($alertPivotActiveFrom, $devicePivotActiveFrom);

        if ($activeFrom && strtotime($activeFrom) > $time)
            return false;

        $alertPivotActiveTo = $this->alert->pivot->active_to ?? null;
        $devicePivotActiveTo = $this->device->pivot->active_to ?? null;
        $activeTo = max($alertPivotActiveTo, $devicePivotActiveTo);

        if ($activeTo && strtotime($activeTo) < $time)
            return false;

        return true;
    }

    /**
     * @param string|int $time
     * @return bool
     */
    protected function checkOccurred($time)
    {
        $alertPivotFiredAt = $this->alert->pivot->fired_at ?? null;
        $devicePivotFiredAt = $this->device->pivot->fired_at ?? null;
        $firedAt = max($alertPivotFiredAt, $devicePivotFiredAt);

        if (empty($firedAt))
            return true;

        if ( ! is_integer($time))
            $time = strtotime($time);

        return strtotime($firedAt) < $time;
    }

    /**
     * @param string $time
     * @return bool
     */
    protected function checkSchedules($time)
    {
        if ( ! $this->checkActiveTime($time))
            return false;

        if ( ! $this->alert->schedule)
            return true;

        if (empty($time))
            return true;

        return $this->scheduleService->inSchedules($time);
    }

    protected function checkZones($latitude, $longitude)
    {
        if (in_array($this->alert->type, ['geofence_in','geofence_out','geofence_inout']))
            return true;

        if ( ! $this->alert->zone)
            return true;

        if (empty($this->alert->zones))
            return true;

        if ($this->alert->zone == 1)
            return $this->checkZonesIn($latitude, $longitude);

        if ($this->alert->zone == 2)
            return $this->checkZonesOut($latitude, $longitude);

        return false;
    }

    protected function checkZonesIn($latitude, $longitude)
    {
        foreach ($this->alert->zones as $zone) {
            if ($zone->pointIn(['latitude' => $latitude, 'longitude' => $longitude]))
                return true;
        }

        return false;
    }

    protected function checkZonesOut($latitude, $longitude)
    {
        foreach ($this->alert->zones as $zone) {
            if ($zone->pointIn(['latitude' => $latitude, 'longitude' => $longitude]))
                return false;
        }

        return true;
    }

    protected function getZone($latitude, $longitude)
    {
        if (in_array($this->alert->type, ['geofence_in','geofence_out','geofence_inout']))
            return null;

        if ( ! $this->alert->zone)
            return null;

        if (empty($this->alert->zones))
            return null;

        if ($this->alert->zone == 1)
            return $this->getZoneIn($latitude, $longitude);

        /*
        if ($this->alert->zone == 2)
            return $this->getZoneOut($latitude, $longitude);
        */

        return null;
    }

    protected function getZoneIn($latitude, $longitude)
    {
        foreach ($this->alert->zones as $zone) {
            if ($zone->pointIn(['latitude' => $latitude, 'longitude' => $longitude]))
                return $zone;
        }

        return null;
    }

    protected function getZoneOut($latitude, $longitude)
    {
        foreach ($this->alert->zones as $zone) {
            if ($zone->pointOut(['latitude' => $latitude, 'longitude' => $longitude]))
                return $zone;
        }

        return null;
    }

    protected function getZones($latitude, $longitude)
    {
        if (in_array($this->alert->type, ['geofence_in','geofence_out','geofence_inout']))
            return null;

        if ( ! $this->alert->zone)
            return null;

        if (empty($this->alert->zones))
            return null;

        if ($this->alert->zone == 1)
            return $this->getZonesIn($latitude, $longitude);

        if ($this->alert->zone == 2)
            return $this->getZonesOut($latitude, $longitude);

        return null;
    }

    protected function getZonesIn($latitude, $longitude)
    {
        $zones = [];

        foreach ($this->alert->zones as $zone) {
            if ($zone->pointIn(['latitude' => $latitude, 'longitude' => $longitude]))
                $zones[] = $zone;
        }

        return $zones;
    }

    protected function getZonesOut($latitude, $longitude)
    {
        $zones = [];

        foreach ($this->alert->zones as $zone) {
            if ($zone->pointOut(['latitude' => $latitude, 'longitude' => $longitude]))
                $zones[] = $zone;
        }

        return $zones;
    }

    /**
     * @return Event
     */
    protected function getEvent()
    {
        $position = $this->getPosition();

        $event = new Event([
            'user_id'      => $this->alert->user_id,
            'alert_id'     => $this->alert->id,
            'device_id'    => $this->device->id,
            'geofence_id'  => null,
            'poi_id'       => null,
            'altitude'     => $position->altitude,
            'course'       => $position->course,
            'latitude'     => $position->latitude,
            'longitude'    => $position->longitude,
            'speed'        => $this->device->getSpeed($position),
            'time'         => $position->time,
            'additional'   => null,
            'message'      => '',
            'silent'       => null,
        ]);

        if ($zone = $this->getZone($position->latitude, $position->longitude))
        {
            $event->geofence_id = $zone->id;
            $event->setAdditional('geofence', $zone->name);
        }

        if ($this->device->current_driver_id && $driver = $this->device->driver) {
            $event->setAdditional('driver_id', $driver->id);
            $event->setAdditional('driver_name', $driver->name);
        }

        $event->setCreatedAt( Carbon::now() );
        $event->setUpdatedAt( Carbon::now() );

        $event->channels = $this->alert->channels;

        return $event;
    }

    protected function silent(Event &$event)
    {
        $notifications = $this->alert->notifications;
        $silent = Arr::get($notifications, 'silent', null);

        if (!($silent && $silent['active'])) {
            return;
        }

        $duration = intval($silent['input']);

        if ($duration < 1) {
            return;
        }

        $exist = Event::whereNull('silent')
            ->where('alert_id', $event->alert_id)
            ->where('device_id', $event->device_id)
            ->where('poi_id', $event->poi_id)
            ->where('geofence_id', $event->geofence_id)
            ->where('type', $event->type)
            ->where('created_at', '>=', $event->created_at->subMinutes($duration))
            ->latest()
            ->first(['id']);

        if (!$exist) {
            return;
        }

        $event->silent = true;
        $event->channels = null;
    }
}