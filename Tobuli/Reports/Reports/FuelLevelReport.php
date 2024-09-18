<?php

namespace Tobuli\Reports\Reports;


use Tobuli\Reports\DeviceSensorDataReport;

class FuelLevelReport extends DeviceSensorDataReport
{
    const TYPE_ID = 10;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];
    protected $formats = ['html', 'json'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.fuel_level');
    }

    protected function getSensorTypes()
    {
        return ['fuel_tank', 'fuel_tank_calibration'];
    }
}