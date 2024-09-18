<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatCount;

class Harsh extends ActionStat
{
    static public function required()
    {
        return [
            AppendHarshAcceleration::class,
            AppendHarshBreaking::class,
            AppendHarshTurning::class,
        ];
    }

    public function boot()
    {
        $this->registerStat('harsh_acceleration_count', (new StatCount()));
        $this->registerStat('harsh_breaking_count', (new StatCount()));
        $this->registerStat('harsh_turning_count', (new StatCount()));
    }

    public function proccess($position)
    {
        if ($position->harsh_acceleration)
            $this->history->applyStat('harsh_acceleration_count', 1);

        if ($position->harsh_breaking)
            $this->history->applyStat('harsh_breaking_count', 1);

        if ($position->harsh_turning)
            $this->history->applyStat('harsh_turning_count', 1);
    }
}