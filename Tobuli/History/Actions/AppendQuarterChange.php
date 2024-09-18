<?php

namespace Tobuli\History\Actions;


class AppendQuarterChange extends ActionAppend
{
    protected $current;

    static public function required()
    {
        return [
            AppendQuarterHour::class,
        ];
    }

    public function boot(){}

    public function proccess(&$position)
    {
        if (is_null($this->current))
            $this->current = $position->quarter;

        $position->quarter_change = $this->current != $position->quarter;

        $this->current = $position->quarter;
    }
}