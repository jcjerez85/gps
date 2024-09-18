<?php

namespace Tobuli\History\Actions;

abstract class ActionGroupGeofence extends ActionGroup
{
    static public function required()
    {
        return [
            AppendGeofences::class
        ];
    }

    public function boot() {}

    protected function geofences( & $position)
    {
        if (empty($position->geofences))
            return [];

        return $position->geofences;
    }

    protected function enterGeofences( & $position)
    {
        $previous = $this->getPrevPosition();

        if ( ! $previous)
            return $this->geofences($position);

        return array_diff($this->geofences($position), $this->geofences($previous));
    }

    protected function leftGeofences( & $position)
    {
        $previous = $this->getPrevPosition();

        if ( ! $previous)
            return [];

        return array_diff($this->geofences($previous), $this->geofences($position));
    }
}