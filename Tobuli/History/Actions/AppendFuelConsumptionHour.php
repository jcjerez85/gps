<?php

namespace Tobuli\History\Actions;

class AppendFuelConsumptionHour extends ActionAppend
{
    protected $fuelPerH = null;

    static public function required()
    {
        return [
            AppendDistance::class,
            AppendDuration::class,
            AppendEngineStatus::class,
        ];
    }

    public function boot()
    {
        $device = $this->getDevice();

        if ($device->fuel_per_h > 0) {
            $this->fuelPerH = $device->fuel_per_h;
        }
    }

    public function proccess(&$position)
    {
        if (!$this->fuelPerH) {
            return;
        }

        if (!$position->engine || $this->isStateChanged($position, 'engine')) {
            return;
        }

        if (empty($position->consumptions)) {
            $position->consumptions = [];
        }

        $position->consumptions['hour'] = $this->fuelPerH * $position->duration / 3600;
    }
}