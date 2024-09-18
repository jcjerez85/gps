<?php

namespace Tobuli\History\Actions;


use Formatter;

class AppendDurationQuarterHour extends ActionAppend
{
    static public function required()
    {
        return [
            AppendDuration::class,
            AppendQuarterHour::class,
            AppendQuarterChange::class,
        ];
    }

    public function boot(){}

    public function proccess(&$position)
    {
        if ($this->isConvertable($position)) {
            $position->duration -= $this->durationFromQuarter($position);
        }

        $previous = $this->getPrevPosition();
        if ($previous && $this->isConvertable($previous)) {
            $position->duration += $this->durationFromQuarter($previous);
        }
    }

    protected function isConvertable($position)
    {
        if ($position->duration > 900)
            return false;

        return $position->quarter_change;
    }

    protected function durationFromQuarter($position)
    {
        return strtotime(Formatter::time()->convert($position->time)) - strtotime($position->quarter);
    }
}