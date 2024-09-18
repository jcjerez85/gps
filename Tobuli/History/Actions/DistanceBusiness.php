<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatSum;

class DistanceBusiness extends ActionStat
{
    static public function required()
    {
        return [
            AppendDistance::class,
            AppendDriveBusiness::class,
        ];
    }

    public function boot()
    {
        $formatter = Formatter::distance();

        $this->registerStat('distance_business', (new StatSum())->setFormatUnit($formatter));
    }

    public function proccess($position)
    {
        if (!empty($position->drive_business)) {
            $this->history->applyStat('distance_business', $position->distance);
        }
    }
}