<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\Odometer;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationReport extends DeviceHistoryReport
{
    const TYPE_ID = 1;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return 1;
    }

    public function title()
    {
        return trans('front.general_information');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            EngineHours::class,
            Drivers::class,
            OverspeedStatic::class,
            Odometer::class,
        ];
    }
}