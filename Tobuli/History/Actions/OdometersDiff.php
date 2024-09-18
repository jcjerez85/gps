<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\StatDiff;

class OdometersDiff extends ActionStat
{
    static public function required()
    {
        return [
            AppendOdometers::class
        ];
    }

    public function boot()
    {
        $device = $this->getDevice();

        $sensors = $device->getSensorsByType('odometer');

        if ( ! $sensors)
            return;

        foreach ($sensors as $sensor)
        {
            $formatter = clone Formatter::distance();
            $formatter->setUnit($sensor->unit_of_measurement);

            $stat = (new StatDiff())->setFormatUnit($formatter);
            $stat->setName( $sensor->formatName() );

            $this->registerStat("odometer_diff_{$sensor->id}", $stat);
        }
    }

    public function proccess($position)
    {
        if (is_null($position->odometers))
            return;

        foreach ($position->odometers as $sensor_id => $value)
        {
            $this->history->applyStat("odometer_diff_{$sensor_id}", $value);
        }
    }
}