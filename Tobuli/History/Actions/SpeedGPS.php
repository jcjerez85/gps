<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatAvg;
use Tobuli\History\Stats\StatMax;
use Tobuli\History\Stats\StatMin;

class SpeedGPS extends ActionStat
{
    static public function required()
    {
        return [
            AppendSpeedECM::class,
        ];
    }

    public function boot()
    {
        $formatter = Formatter::speed();

        $this->registerStat('speed_gps_max', (new StatMax())->setFormatUnit($formatter));
        $this->registerStat('speed_gps_min', (new StatMin())->setFormatUnit($formatter));
        $this->registerStat('speed_gps_avg', (new StatAvg())->setFormatUnit($formatter));
    }

    public function proccess($position)
    {
        $this->history->applyStat('speed_gps_max', $position->speed_gps);
        $this->history->applyStat('speed_gps_min', $position->speed_gps);

        if ($position->speed_gps > 0)
            $this->history->applyStat('speed_gps_avg', $position->speed_gps);
    }
}