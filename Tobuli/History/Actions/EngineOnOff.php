<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\Duration AS DurationStat;

class EngineOnOff extends ActionStat
{
    static public function required()
    {
        return [
            AppendEngineStatus::class,
        ];
    }

    public function boot()
    {
        $this->registerStat('engine_on_duration', (new DurationStat()));
        $this->registerStat('engine_off_duration', (new DurationStat()));
    }

    public function proccess($position)
    {
        if ($position->engine)
            $this->history->applyStat('engine_on_duration', $position->duration);
        else
            $this->history->applyStat('engine_off_duration', $position->duration);
    }
}