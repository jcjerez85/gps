<?php

namespace Tobuli\History\Actions;


class AppendSeatbelt extends ActionAppend
{
    protected $sensor;

    static public function required()
    {
        return [];
    }

    public function boot()
    {
        $this->sensor = $this->getSensor('seatbelt');
    }

    public function proccess(&$position)
    {
        $position->seatbelt = $this->getSensorValue($this->sensor, $position, null);
    }
}