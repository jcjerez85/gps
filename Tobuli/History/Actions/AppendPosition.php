<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatCount;

class AppendPosition extends ActionAppend
{
    public function boot() {}

    public function proccess(&$position)
    {
        $position->timestamp = strtotime($position->time);
        $position->latitude = $position->latitude ? round($position->latitude, 6) : $position->latitude;
        $position->longitude = $position->longitude ? round($position->longitude, 6) : $position->longitude;
    }
}