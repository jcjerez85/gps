<?php

namespace Tobuli\History\Actions;


class BreakGeofenceIn extends ActionBreak
{
    static public function required()
    {
        return [
            AppendGeofences::class,
        ];
    }

    public function boot() {}

    protected function isBreakable($position)
    {
        return empty($position->geofences);
    }
}