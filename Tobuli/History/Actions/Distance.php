<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatSum;

class Distance extends ActionStat
{
    static public function required()
    {
        return [
            AppendDistance::class
        ];
    }

    public function boot()
    {
        $formatter = Formatter::distance();

        $this->registerStat('distance', (new StatSum())->setFormatUnit($formatter));
    }

    public function proccess($position)
    {
        $this->history->applyStat('distance', $position->distance);
    }
}