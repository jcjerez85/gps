<?php

namespace Tobuli\History\Actions;


class AppendGeofencesOnlyOverspeed extends ActionAppend
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
        if(empty($position->overspeeding))
            $position->geofences = [];
    }
}