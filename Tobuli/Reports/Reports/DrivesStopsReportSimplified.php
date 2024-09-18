<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\GroupDriveStop;
use Tobuli\History\Actions\Harsh;
use Tobuli\History\Actions\OdometersDiff;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;

class DrivesStopsReportSimplified extends DrivesStopsReport
{
    const TYPE_ID = 41;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.report_drives_stops_simlified');
    }

    protected function getActionsList()
    {
        $list = [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            EngineHours::class,
            Drivers::class,
            OdometersDiff::class,
            OverspeedStatic::class,
            Harsh::class,

            GroupDriveStop::class,
        ];

        if ($this->zones_instead)
            $list[] = GeofencesIn::class;

        return $list;
    }
}