<?php

namespace Tobuli\History\Actions;


class AppendOdometer extends ActionAppend
{
    protected $sensor;

    static public function required(){
        return [
            AppendOdometerVirtualDistance::class
        ];
    }

    public function boot()
    {
        $this->sensor = $this->getSensor('odometer');
    }

    public function proccess(&$position)
    {
        $position->odometer = null;

        if ( ! $this->sensor)
            return;

        $position->odometer = $this->getSensorValue($this->sensor, $position);
    }
}