<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatSum;

class DiemRate extends ActionStat
{
    static public function required()
    {
        return [
            AppendDiemRate::class,
        ];
    }

    public function boot()
    {
        $formatter = \Formatter::currency();

        $this->registerStat('diem_rate', (new StatSum())->setFormatUnit($formatter));
    }

    public function proccess($position)
    {
        if (isset($position->diem_rate)) {
            $this->history->applyStat('diem_rate', $position->diem_rate);
        }
    }
}