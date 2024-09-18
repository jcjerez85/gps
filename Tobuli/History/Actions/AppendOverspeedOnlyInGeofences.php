<?php

namespace Tobuli\History\Actions;


class AppendOverspeedOnlyInGeofences extends ActionAppend
{
    static public function required()
    {
        return [
            AppendGeofences::class,
            AppendOverspeeding::class,
        ];
    }

    public function boot() {}

    public function proccess(&$position)
    {
        if(empty($position->geofences))
            $position->overspeeding = 0;
    }
}