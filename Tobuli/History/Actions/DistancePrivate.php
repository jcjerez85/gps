<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatSum;

class DistancePrivate extends ActionStat
{
    static public function required()
    {
        return [
            AppendDistance::class,
            AppendDrivePrivate::class,
        ];
    }

    public function boot()
    {
        $formatter = Formatter::distance();

        $this->registerStat('distance_private', (new StatSum())->setFormatUnit($formatter));
    }

    public function proccess($position)
    {
        if (!empty($position->drive_private)) {
            $this->history->applyStat('distance_private', $position->distance);
        }
    }
}