<?php

namespace Tobuli\History\Actions;


class AppendSpeedLimitGeofence extends ActionAppend
{
    private $limits = [];

    static public function required()
    {
        return [
            AppendGeofences::class,
        ];
    }

    public function boot()
    {
    }

    public function proccess(&$position)
    {
        $position->speed_limit = $this->getSpeedLimit($position);
    }

    protected function getSpeedLimit($position)
    {
        if (empty($position->geofences))
            return null;

        foreach ($position->geofences as $geofence_id) {
            $speed_limit = $this->getSpeedLimitGeofence($geofence_id);

            if (!empty($speed_limit))
                break;
        }

        return empty($speed_limit) ? null : $speed_limit;
    }

    public function getSpeedLimitGeofence($geofence_id) {
        $speed_limit = $this->limits[$geofence_id] ?? null;

        if (!is_null($speed_limit))
            return $speed_limit;

        $geofences = $this->history->getGeofences();

        foreach ($geofences as $geofence) {
            if ($geofence->id != $geofence_id)
                continue;

            $speed_limit = empty($geofence->speed_limit) ? 0 : $geofence->speed_limit;
            $this->limits[$geofence_id] = $speed_limit;

            break;
        }

        return $speed_limit;
    }
}