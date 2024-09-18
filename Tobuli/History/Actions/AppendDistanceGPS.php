<?php

namespace Tobuli\History\Actions;


class AppendDistanceGPS extends ActionAppend
{
    static public function required()
    {
        return [
            AppendPosition::class,
        ];
    }

    public function boot(){

    }

    public function proccess(&$position)
    {
        if (isset($position->distance_gps))
            return;

        $position->distance_gps = 0;

        if ($prevPosition = $this->getPrevPosition())
            $position->distance_gps = getDistance(
                $position->latitude,
                $position->longitude,
                $prevPosition->latitude,
                $prevPosition->longitude
            );
    }
}