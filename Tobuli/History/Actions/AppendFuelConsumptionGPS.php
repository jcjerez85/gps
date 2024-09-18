<?php

namespace Tobuli\History\Actions;

class AppendFuelConsumptionGPS extends ActionAppend
{
    protected $fuel_per_km;

    static public function required()
    {
        return [
            AppendDistance::class
        ];
    }

    public function boot()
    {
        $device = $this->getDevice();

        $fuel_per_km = (float) $device->fuel_per_km;

        if ( $fuel_per_km > 0)
        {
            $this->fuel_per_km = $fuel_per_km;
        }
    }

    public function proccess(& $position)
    {
        if (empty($this->fuel_per_km))
            return;

        if (empty($position->consumptions))
            $position->consumptions = [];

        $position->consumptions['gps'] = $position->distance * $this->fuel_per_km;
    }
}