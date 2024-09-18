<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatConsumption;
use Tobuli\History\Stats\StatValue;
use Tobuli\History\Stats\StatValueFirst;

class Fuel extends ActionStat
{
    protected $fuel_price;

    protected $total_by;

    protected $formatters = [];

    static public function required()
    {
        return [
            AppendFuelConsumptions::class
        ];
    }

    public function boot()
    {
        $device = $this->getDevice();
        $this->fuel_price = (float)($device->fuel_price) * ($device->fuel_measurement_id == 2 ? 0.264172053 : 1);

        $this->loadConsumptionGPS();
        $this->loadConsumptionHour();
        $this->loadConsumptionLevelSensors();
        $this->loadConsumptionSensors();

        if ($this->total_by) {
            $this->registerStat("fuel_consumption", (new StatConsumption())->setFormatUnit(Formatter::capacity()));

            if ($this->fuel_price) {
                $this->registerStat("fuel_price", (new StatConsumption()));
            }
        }
    }

    public function proccess($position)
    {
        $this->processFuelTank($position);
        $this->processConsumptions($position);
    }

    protected function processFuelTank($position)
    {
        if (empty($position->fuel_tanks))
            return;

        foreach ($position->fuel_tanks as $key => $value)
        {
            $this->history->applyStat("fuel_level_start_{$key}", $value);
            $this->history->applyStat("fuel_level_end_{$key}", $value);
        }
    }

    protected function processConsumptions($position)
    {
        if ( ! $position->consumptions)
            return;

        $consumption = null;

        foreach ($position->consumptions as $key => $value)
        {
            if ($formatter = $this->formatters[$key] ?? null) {
                $value = $formatter->reverse($value);
            }

            $this->history->applyStat("fuel_consumption_{$key}", $value);

            if ($this->fuel_price) {
                $this->history->applyStat("fuel_price_{$key}", $value * $this->fuel_price);
            }

            if (in_array($key, $this->total_by)) {
                $consumption += $value;
            }
        }

        if ( ! is_null($consumption)) {
            $this->history->applyStat("fuel_consumption", $consumption);

            if ($this->fuel_price)
                $this->history->applyStat("fuel_price", $consumption * $this->fuel_price);
        }
    }

    protected function loadConsumptionGPS()
    {
        if ($this->getDevice()->fuel_per_km <= 0)
            return;

        $this->total_by = ['gps'];

        $this->registerFuelConsumptionStat('GPS', 'gps');
        $this->registerFuelPriceStat('GPS', 'gps');
    }

    protected function loadConsumptionHour()
    {
        $device = $this->getDevice();

        if (!$device->fuel_per_h || $device->fuel_per_h <= 0 || $device->fuel_measurement_id != 4) {
            return;
        }

        $this->total_by = ['hour'];

        $this->registerFuelConsumptionStat('Hour', 'hour');
        $this->registerFuelPriceStat('Hours', 'hour');
    }

    protected function loadConsumptionSensors()
    {
        $sensors = $this->getDevice()->sensors->filter(function($sensor) {
            return in_array($sensor->type, ['fuel_consumption']);
        });

        if ($sensors->isEmpty())
            return;

        $this->total_by = [];

        foreach ($sensors as $sensor)
        {
            $this->total_by[] = $sensor->id;

            $name = $sensor->formatName();
            $formatter = (clone Formatter::capacity())->setUnit($sensor->unit_of_measurement);
            $this->formatters[$sensor->id] = $formatter;

            $this->registerFuelConsumptionStat($name, $sensor->id, $formatter);
            $this->registerFuelPriceStat($name, $sensor->id);
        }
    }

    protected function loadConsumptionLevelSensors()
    {
        $sensors = $this->getDevice()->sensors->filter(function($sensor) {
            return in_array($sensor->type, ['fuel_tank', 'fuel_tank_calibration']);
        });

        if ($sensors->isEmpty())
            return;

        $this->total_by = [];

        foreach ($sensors as $sensor)
        {
            $this->total_by[] = $sensor->id;

            $name = $sensor->formatName();
            $formatter = (clone Formatter::capacity())->setUnit($sensor->unit_of_measurement);
            $this->formatters[$sensor->id] = $formatter;

            $this->registerFuelConsumptionStat($name, $sensor->id, $formatter);
            $this->registerFuelPriceStat($name, $sensor->id);


            $stat = (new StatValueFirst())->setFormatUnit($formatter);
            $stat->setName($name);
            $this->registerStat("fuel_level_start_{$sensor->id}", $stat);

            $stat = (new StatValue())->setFormatUnit($formatter);
            $stat->setName($name);
            $this->registerStat("fuel_level_end_{$sensor->id}", $stat);
        }
    }

    protected function registerFuelPriceStat($name, $key)
    {
        if (!$this->fuel_price) {
            return;
        }

        $stat = new StatConsumption();
        $stat->setName($name);
        $this->registerStat("fuel_price_{$key}", $stat);
    }

    protected function registerFuelConsumptionStat($name, $key, $formatter = null)
    {
        $stat = (new StatConsumption())->setFormatUnit($formatter ?? Formatter::capacity());
        $stat->setName($name);

        $this->registerStat("fuel_consumption_{$key}", $stat);
    }
}