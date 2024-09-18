<?php

namespace Tobuli\History\Actions;

class AppendFuelConsumptionLevelSensors extends ActionAppend
{
    protected $sensors = [];

    protected $consumptions = [];

    static public function required()
    {
        return [
            AppendFuelTanks::class,
            AppendFuelFilling::class,
        ];
    }

    public function boot()
    {
        $device = $this->getDevice();

        $this->sensors = $device->sensors
            ->filter(function($sensor) {
                return in_array($sensor->type, ['fuel_tank', 'fuel_tank_calibration']);
            });
    }

    public function proccess(& $position)
    {
        if (empty($position->consumptions))
            $position->consumptions = [];

        foreach ($this->sensors as $sensor)
            $position->consumptions[$sensor->id] = $this->getConsumptionValue($sensor, $position);

        if (!empty($position->fuel_filling)) {
            $position->consumptions[$position->fuel_filling['sensor_id']] += $position->fuel_filling['diff'];
        }
    }

    protected function getConsumptionValue($sensor, & $position)
    {
        $prevPosition = $this->getPrevPosition();

        if ( ! $prevPosition)
            return null;

        $value     = $position->fuel_tanks[$sensor->id];
        $prevValue = $prevPosition->fuel_tanks[$sensor->id];

        if (empty($value) || empty($prevValue))
            return 0;

        return $prevValue - $value;
    }
}