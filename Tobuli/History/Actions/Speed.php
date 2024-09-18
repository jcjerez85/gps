<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatAvg;
use Tobuli\History\Stats\StatMax;
use Tobuli\History\Stats\StatMin;

class Speed extends ActionStat
{
    static public function required()
    {
        return [
            AppendSpeed::class,
        ];
    }

    public function boot()
    {
        $formatter = Formatter::speed();

        $this->registerStat('speed_max', (new StatMax())->setFormatUnit($formatter));
        $this->registerStat('speed_min', (new StatMin())->setFormatUnit($formatter));
        $this->registerStat('speed_avg', (new StatAvg())->setFormatUnit($formatter));
    }

    public function proccess($position)
    {
        $this->history->applyStat('speed_max', $position->speed);
        $this->history->applyStat('speed_min', $position->speed);

        if ($position->speed > 0)
            $this->history->applyStat('speed_avg', $position->speed);
    }
}