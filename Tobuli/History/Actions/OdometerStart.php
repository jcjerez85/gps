<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatValueFirst;

class OdometerStart extends ActionStat
{
    static public function required()
    {
        return [
            AppendOdometer::class
        ];
    }

    public function boot()
    {
        $sensor = $this->getSensor('odometer');

        if ( ! $sensor)
            return;

        $this->registerStat('odometer_start', (new StatValueFirst())->setMeasure($sensor->unit_of_measurement));
    }

    public function proccess($position)
    {
        if (is_null($position->odometer))
            return;

        if ($position->odometer)
            $this->history->applyStat('odometer_start', $position->odometer);

    }
}