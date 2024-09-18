<?php

namespace Tobuli\History\Actions;


class AppendSpeed extends ActionAppend
{
    protected $sensor;

    static public function required()
    {
        return [];
    }

    public function boot()
    {
        $this->sensor = $this->getDevice()->getSpeedSensor();
    }

    public function proccess(&$position)
    {
        $position->speed_gps = $position->speed;

        if ( ! $this->sensor)
            return;

        $position->speed = $this->sensor->getValue($position->other, false, 0);
    }
}