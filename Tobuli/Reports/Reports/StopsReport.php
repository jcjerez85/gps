<?php namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\GroupStop;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\OdometersDiff;
use Tobuli\History\Actions\Speed;

class StopsReport extends DrivesStopsReport
{
    const TYPE_ID = 40;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.stops');
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

            GroupStop::class,
        ];

        if ($this->zones_instead)
            $list[] = GeofencesIn::class;

        return $list;
    }
}