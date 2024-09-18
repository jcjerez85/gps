<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\Duration AS DurationStat;

class Seatbelt extends ActionStat
{
    static public function required()
    {
        return [
            AppendSeatbelt::class,
            AppendSpeed::class,
        ];
    }

    public function boot()
    {
        $this->registerStat('seatbelt_on_duration', (new DurationStat()));
        $this->registerStat('seatbelt_off_duration', (new DurationStat()));
    }

    public function proccess($position)
    {
        if (is_null($position->seatbelt))
            return;

        if ($position->speed <= 0)
            return;

        if ($position->seatbelt)
            $this->history->applyStat('seatbelt_on_duration', $position->duration);
        else
            $this->history->applyStat('seatbelt_off_duration', $position->duration);
    }
}