<?php

namespace Tobuli\History\Actions;


class AppendDayChange extends ActionAppend
{
    protected $current;

    static public function required()
    {
        return [
            AppendDateUserZone::class,
        ];
    }

    public function boot(){}

    public function proccess(&$position)
    {
        if (is_null($this->current))
            $this->current = $position->date;

        $position->day_change = $this->current != $position->date;

        $this->current = $position->date;
    }
}