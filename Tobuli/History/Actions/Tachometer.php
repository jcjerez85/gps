<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatValue;

class Tachometer extends ActionStat
{
    static public function required()
    {
        return [
            AppendTachometerSensor::class,
        ];
    }

    public function boot()
    {
        $sensor = $this->getSensor('tachometer');

        if ( ! $sensor) return;

        $this->registerStat('tachometer', (new StatValue()));
    }

    public function proccess($position)
    {
        if (is_null($position->tachometer))
            return;

        $this->history->applyStat('tachometer', $position->tachometer);
    }
}