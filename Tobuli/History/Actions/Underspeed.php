<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Stats\Duration AS DurationStat;
use Tobuli\History\Stats\StatCount;

class Underspeed extends ActionStat
{
    static public function required()
    {
        return [
            AppendUnderspeed::class,
            AppendDuration::class,
        ];
    }

    public function boot()
    {
        $this->registerStat('underspeed_count', new StatCount());
        $this->registerStat('underspeed_duration', new DurationStat());
        $this->registerStat('underspeed_positions', new StatCount());
    }

    public function proccess($position)
    {
        if ( ! $this->isUnderspeed($position))
            return;

        $this->history->applyStat("underspeed_positions", 1);
        $this->history->applyStat("underspeed_duration", $position->duration);

        if ($this->isStart($position))
            $this->history->applyStat("underspeed_count", 1);
    }

    protected function isStart($position)
    {
        return $position->underspeeding == 1;
    }

    protected function isUnderspeed($position)
    {
        return $position->underspeeding;
    }
}