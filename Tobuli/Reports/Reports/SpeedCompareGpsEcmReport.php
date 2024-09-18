<?php namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Database\QueryException;
use Tobuli\Reports\DeviceReport;

class SpeedCompareGpsEcmReport extends DeviceReport
{
    const TYPE_ID = 51;

    protected $speed_sensor;
    protected $tachometer_sensor;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.speed_compare_gps_ecm_report');
    }

    public static function isEnabled()
    {
        return settings('plugins.speed_compare_gps_ecm_report.status');
    }

    protected function processPosition($position)
    {
        if ($this->speed_limit && $position->speed < $this->speed_limit)
            return null;

        $speed_ecm = $this->speed_sensor->getValue($position->other, false, 0);
        $difference = abs($position->speed - $speed_ecm);

        if ($difference < 5)
            return null;

        $tachometer = null;
        if ($this->tachometer_sensor)
            $tachometer = $this->tachometer_sensor->getValue($position->other, false);

        return [
            'time'       => Formatter::time()->human($position->time),
            'speed'      => Formatter::speed()->human($position->speed),
            'speed_ecm'  => Formatter::speed()->human($speed_ecm),
            'difference' => Formatter::speed()->human($difference),
            'tachometer' => $tachometer,
            'location'   => $this->getLocation($position),
        ];
    }

    protected function generateDevice($device)
    {
        $this->speed_sensor = $device->getSensorByType('speed_ecm');
        $this->tachometer_sensor = $device->getSensorByType('tachometer');

        if (empty($this->speed_sensor))
            return [
                'meta'  => $this->getDeviceMeta($device),
                'error' => dontExist('front.sensor'),
            ];

        $rows = [];

        try {
            $device->positions()
                ->orderliness('asc')
                ->whereBetween('time', [$this->date_from, $this->date_to])
                ->chunk(2000,
                    function ($positions) use (& $rows) {
                        foreach ($positions as $position) {
                            $row = $this->processPosition($position);

                            if (!$row)
                                continue;

                            $rows[] = $row;
                        }
                    });
        } catch (QueryException $e) {}

        if (empty($rows))
            return null;

        return [
            'meta'       => $this->getDeviceMeta($device),
            'table'      => [
                'rows' => $rows,
            ],
        ];
    }
}