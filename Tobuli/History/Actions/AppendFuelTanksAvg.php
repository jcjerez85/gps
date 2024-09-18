<?php

namespace Tobuli\History\Actions;

class AppendFuelTanksAvg extends ActionAppend
{
    protected $sensors = [];

    protected $collection = [];

    protected $fuel_tanks_avg = [];

    static public function required()
    {
        return [
            AppendFuelTanks::class,
        ];
    }

    public function boot()
    {
        $this->min_duration = 15 * 60;

        $device = $this->getDevice();

        $this->sensors = $device->sensors
            ->filter(function($sensor) {
                return in_array($sensor->type, ['fuel_tank', 'fuel_tank_calibration']);
            });

        $this->fuel_tanks_avg = array_fill_keys($this->sensors->pluck('id')->all(), null);
    }

    public function proccess(& $position)
    {
        $this->addCollection($position);

        $position->fuel_tanks_avg = $this->fuel_tanks_avg;

        $count = count($this->collection);

        foreach ($this->sensors as $sensor) {
            foreach ($this->collection as $_position) {
                $position->fuel_tanks_avg[$sensor->id] += $_position->fuel_tanks[$sensor->id];
            }

            $position->fuel_tanks_avg[$sensor->id] /= $count;
        }
    }

    protected function addCollection($current) {
        $this->collection = array_filter($this->collection, function($position) use ($current) {
            if ($position->id == $current->id)
                return false;

            if ($current->timestamp - $position->timestamp > $this->min_duration)
                return false;

            return true;
        });

        $this->collection[] = $current;
    }
}