<?php

namespace Tobuli\Helpers\Alerts;

use Illuminate\Support\Str;
use Tobuli\Entities\Alert;

class Checker {

    protected $alerts;
    protected $device;

    protected $checkers = [];

    public function __construct($device, $alerts)
    {
        $this->setDevice($device);
        $this->setAlerts($alerts);
    }

    public function setDevice($device)
    {
        $this->device = $device;
    }

    public function setAlerts($alerts)
    {
        $this->alerts = $alerts;

        $this->loadCheckers();
    }

    public function check($position = null, $prevPosition = null)
    {
        $events = [];

        foreach ($this->checkers as $checker) {
            $checker->setDevice($this->device);
            $checker->setCurrentPosition($position);
            $checker->setPreviousPosition($prevPosition);

            if ($_events = $checker->getEvents())
                $events = array_merge($events, $_events);
        }

        return $events;
    }

    protected function loadCheckers()
    {
        $this->checkers = [];

        if (empty($this->alerts))
            return;

        foreach ($this->alerts as $alert) {
            if (empty($alert->type))
                continue;

            if (!$alert->user)
                continue;

            if (!$alert->user->isCapable())
                continue;

            $checker = $this->alertChecker($alert);

            if (empty($checker))
                continue;

            $this->checkers[] = $checker;
        }
    }

    protected function alertChecker(Alert $alert) {
        switch($alert->type) {
            case 'geofence_in':
            case 'geofence_out':
            case 'geofence_inout':
                $checker = new GeofenceAlertCheck($this->device, $alert);
                break;
            case 'custom':
                $checker = new EventCustomAlertCheck($this->device, $alert);
                break;
            case 'fuel_change':
                $checker = new FuelLevelChangeCheck($this->device, $alert);
                break;
            case 'task_status':
                $checker = new BaseAlertCheck($this->device, $alert);
                break;
            default:
                $class = 'Tobuli\Helpers\Alerts\\' . ucfirst(Str::camel($alert->type)) . 'AlertCheck';

                if ( ! class_exists($class, true)) {
                    throw new \Exception('Alert type "'.$alert->type.'" doesnt have check class.');
                }

                $checker = new $class($this->device, $alert);
        }

        return $checker;
    }
}