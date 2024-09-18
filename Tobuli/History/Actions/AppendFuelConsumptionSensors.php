<?php

namespace Tobuli\History\Actions;

class AppendFuelConsumptionSensors extends ActionAppend
{
    protected $sensors;

    static public function required()
    {
        return [];
    }

    public function boot()
    {
        $device = $this->getDevice();

        $this->sensors = $device->sensors
            ->filter(function($sensor) {
                return in_array($sensor->type, ['fuel_consumption']);
            });
    }

    public function proccess(& $position)
    {
        if (empty($position->consumptions))
            $position->consumptions = [];

        foreach ($this->sensors as $sensor) {
            switch ($sensor->shown_value_by) {
                case 'separate':
                    $value = $this->getConsumptionSeparateValue($sensor, $position);
                    break;
                default:
                    $value = $this->getConsumptionIncrementalValue($sensor, $position);
            }

            $position->consumptions[$sensor->id] = $value;
        }
    }

    protected function getConsumptionSeparateValue($sensor, & $position)
    {
        $value = $this->getSensorValue($sensor, $position);

        if (empty($value))
            return 0;

        return $value;
    }

    protected function getConsumptionIncrementalValue($sensor, & $position)
    {
        $prevPosition = $this->getPrevPosition();

        if ( ! $prevPosition)
            return null;

        $value     = $this->getSensorValue($sensor, $position);
        $prevValue = $this->getSensorValue($sensor, $prevPosition);

        if (empty($value) || empty($prevValue))
            return 0;

        return $value - $prevValue;
    }
}