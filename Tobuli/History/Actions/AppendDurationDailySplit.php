<?php

namespace Tobuli\History\Actions;


use Formatter;

class AppendDurationDailySplit extends ActionAppend
{
    static public function required()
    {
        return [
            AppendDuration::class,
            AppendDateUserZone::class,
            AppendDayChange::class,
        ];
    }

    public function boot(){}

    public function proccess(&$position)
    {
        if ($this->isConvertable($position)) {
            $position->duration -= $this->durationFromMidnight($position);
        }

        $previous = $this->getPrevPosition();
        if ($previous && $this->isConvertable($previous)) {
            $position->duration += $this->durationFromMidnight($previous);
        }
    }

    protected function isConvertable($position)
    {
        // 48h gap
        if ($position->duration > 172800)
            return false;

        return $position->day_change;
    }

    protected function durationFromMidnight($position)
    {
        return strtotime(Formatter::time()->convert($position->time)) - strtotime($position->date);
    }
}