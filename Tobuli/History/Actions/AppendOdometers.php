<?php

namespace Tobuli\History\Actions;


class AppendOdometers extends ActionAppend
{
    protected $sensors;

    static public function required(){
        return [
            AppendOdometerVirtualDistance::class
        ];
    }

    public function boot()
    {
        $this->sensors = $this->getDevice()->getSensorsByType('odometer');
    }

    public function proccess(&$position)
    {
        $position->odometers = [];

        if ( ! $this->sensors)
            return;

        foreach ($this->sensors as & $sensor) {
            $position->odometers[$sensor->id] = $this->getSensorValue($sensor, $position);
        }
    }
}