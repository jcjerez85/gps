<?php

namespace Tobuli\History\Actions;

class AppendFuelTanksDiff extends ActionAppend
{
    const DURATION_CHANGE = 5*60;

    protected $sensors = [];

    protected $collection = [];

    protected $fuel_tanks_diff = [];

    static public function required()
    {
        return [
            AppendFuelTanks::class,
        ];
    }

    public function boot()
    {
        $device = $this->getDevice();

        $this->sensors = $device->sensors
            ->filter(function($sensor) {
                return in_array($sensor->type, ['fuel_tank', 'fuel_tank_calibration']);
            });

        $this->fuel_tanks_diff = array_fill_keys($this->sensors->pluck('id')->all(), null);
    }

    public function proccess(& $position)
    {
        $position->fuel_tanks_diff = $this->fuel_tanks_diff;

        if (empty($this->sensors))
            return;

        $this->addCollection($position);

        foreach ($this->sensors as $sensor) {

            $_previous = null;

            foreach ($this->collection as $_position) {
                if (is_null($_previous)) {
                    $_previous = $_position;
                    continue;
                }

                $value = (float)$_position->fuel_tanks[$sensor->id];
                $prevValue = (float)$_previous->fuel_tanks[$sensor->id];

                $_previous = $_position;

                if (empty($value) || empty($prevValue))
                    continue;

                $position->fuel_tanks_diff[$sensor->id] += $value - $prevValue;
            }
        }
    }

    protected function addCollection($current) {
         $collection = array_filter($this->collection, function($position) use ($current) {
            if ($position->id == $current->id)
                return false;

            if ($current->timestamp - $position->timestamp > self::DURATION_CHANGE)
                return false;

            return true;
        });

        if (empty($collection))
            $collection = array_slice($this->collection, -3, 3, true);

        $this->collection = $collection;

        $this->collection[] = $current;
    }
}