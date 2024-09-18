<?php

namespace Tobuli\History\Actions;


class AppendGeofences extends ActionAppend
{
    public static function required()
    {
        return [
            AppendPosition::class,
        ];
    }

    public static function after()
    {
        return [
            AppendDiemRateGeofencesOverwrite::class,
            AppendOverspeedingProcessOnly::class,
        ];
    }

    public function boot(){}

    public function proccess(&$position)
    {
        if (property_exists($position, 'geofences'))
            return;

        if (property_exists($position, 'only_overspeeding') && $position->only_overspeeding === false)
            return;

        $position->geofences = $this->history->inGeofences($position);
    }
}