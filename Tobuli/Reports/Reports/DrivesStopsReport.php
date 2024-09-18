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
use Tobuli\History\Actions\OdometerEnd;
use Tobuli\History\Actions\OdometersDiff;
use Tobuli\History\Actions\OdometerStart;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class DrivesStopsReport extends DeviceHistoryReport
{
    const TYPE_ID = 3;

    protected $disableFields = ['geofences', 'speed_limit'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.drives_and_stops');
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
            OdometerStart::class,
            OdometerEnd::class,

            GroupDriveStop::class,
        ];

        if ($this->zones_instead)
            $list[] = GeofencesIn::class;

        return $list;
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $rows[] = $this->getDataFromGroup($group, [
                'group_key',
                'status',
                'start_at',
                'end_at',
                'duration',
                'distance',
                'engine_idle',
                'drivers',
                'speed_max',
                'speed_avg',
                'location',
                'fuel_consumption',
                'geofences_in'
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }
}