<?php

namespace Tobuli\History\Actions;


class AppendOdometerVirtualDistance extends ActionAppend
{
    protected $sensors = [];

    static public function required(){
        return [
            AppendDistanceGPS::class
        ];
    }

    public function boot()
    {
        $sensors = $this->getDevice()->getSensorsByType('odometer');

        if ( ! $sensors)
            return;

        $distance = null;

        foreach ($sensors as & $sensor)
        {
            if ($sensor->odometer_value_by != 'virtual_odometer')
                continue;

            if (is_null($distance))
                $distance = $this->getDevice()->getSumDistance($this->getDateFrom());

            $sensor->odometer_value = round($sensor->odometer_value - $distance);

            $this->sensors[] = $sensor;
        }
    }

    public function proccess(&$position)
    {
        foreach ($this->sensors as $sensor) {
            $sensor->odometer_value += $position->distance_gps;
        }
    }
}