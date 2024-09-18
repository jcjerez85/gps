<?php

namespace Tobuli\History\Actions;


class AppendDistance extends ActionAppend
{
    static public function required()
    {
        return [
            AppendDistanceGPS::class,
        ];
    }

    public function boot(){

    }

    public function proccess(&$position)
    {
        $position->distance = $position->distance_gps;
    }
}