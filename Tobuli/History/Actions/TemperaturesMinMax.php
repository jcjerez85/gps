<?php

namespace Tobuli\History\Actions;

use Tobuli\Entities\DeviceSensor;
use Tobuli\History\Stats\StatMax;
use Tobuli\History\Stats\StatMin;

class TemperaturesMinMax extends ActionAppend
{
    /**
     * @var array
     */
    private $sensors;

    static public function required()
    {
        return [SensorsValues::class];
    }

    public function boot()
    {
        $this->sensors = $this->history->sensors->filter(function(DeviceSensor $sensor) {
            return in_array($sensor->type, ['temperature_calibration', 'temperature']);
        });

        /** @var DeviceSensor $sensor */
        foreach ($this->sensors as $sensor) {
            $this->registerStat('temperature_max_' . $sensor->id, (new StatMax())->setMeasure($sensor->unit_of_measurement));
            $this->registerStat('temperature_min_' . $sensor->id, (new StatMin())->setMeasure($sensor->unit_of_measurement));
        }
    }

    public function proccess(&$position)
    {
        foreach ($this->sensors as $sensor) {
            $temperature = $this->getSensorValue($sensor, $position);

            $this->history->applyStat('temperature_max_' . $sensor->id, $temperature);
            $this->history->applyStat('temperature_min_' . $sensor->id, $temperature);
        }
    }
}