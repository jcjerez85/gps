<?php

namespace Tobuli\Reports;

use Tobuli\History\DeviceHistory;

abstract class DeviceSensorDataReport extends DeviceHistoryReport
{
    abstract protected function getSensorTypes();

    protected function getActionsList()
    {
        return [];
    }

    protected function getDeviceHistoryData($device)
    {
        $history = new DeviceHistory($device);

        $history->setConfig([
            'stop_seconds'      => $this->stop_seconds,
            'speed_limit'       => $this->speed_limit,
            'stop_speed'        => $device->min_moving_speed,
            'min_fuel_fillings' => $device->min_fuel_fillings,
            'min_fuel_thefts'   => $device->min_fuel_thefts,
        ]);

        $types = $this->getSensorTypes();
        $sensors = $device->sensors->filter(function($sensor) use ($types) {
            return in_array($sensor->type, $types);
        });

        if ( ! $sensors)
            return null;

        $history->setSensors($sensors);

        $history->registerActions(
            $this->getActionsList()
        );

        $history->setRange($this->date_from, $this->date_to);
        $history->get();

        return $history;
    }

    protected function generateDevice($device)
    {
        $history = $this->getDeviceHistoryData($device);

        if ( ! $history)
            return null;

        $data = $history->getSensorsData();

        if ( ! $data)
            return null;

        $response = [
            'meta'    => $this->getDeviceMeta($device),
            'sensors' => $data,
        ];

        if ($totals = $this->getStats($history)) {
            $response['stats'] = $totals;
        }

        return $response;
    }

    protected function getStats(DeviceHistory $history)
    {
        return null;
    }
}