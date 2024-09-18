<?php

namespace Tobuli\History\Actions;

class AppendFuelTanks extends ActionAppend
{
    protected $sensors = [];

    protected $fuel_tanks = [];

    static public function required()
    {
        return [];
    }

    public function boot()
    {
        $device = $this->getDevice();

        $this->sensors = $device->sensors
            ->filter(function($sensor) {
                return in_array($sensor->type, ['fuel_tank', 'fuel_tank_calibration']);
            });

        $this->fuel_tanks = array_fill_keys($this->sensors->pluck('id')->all(), null);
    }

    public function proccess(& $position)
    {
        foreach ($this->sensors as $sensor) {
            $value = $this->getSensorValue($sensor, $position);

            if (!is_null($value))
                $value = floatval($value);

            $this->fuel_tanks[$sensor->id] = $value;
        }

        $position->fuel_tanks = $this->fuel_tanks;
    }
}