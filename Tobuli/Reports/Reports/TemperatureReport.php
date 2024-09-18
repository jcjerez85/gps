<?php

namespace Tobuli\Reports\Reports;


use Tobuli\Reports\DeviceSensorDataReport;

class TemperatureReport extends DeviceSensorDataReport
{
    const TYPE_ID = 13;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];
    protected $formats = ['html', 'json'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.temperature');
    }

    protected function getSensorTypes()
    {
        return ['temperature_calibration', 'temperature'];
    }
}