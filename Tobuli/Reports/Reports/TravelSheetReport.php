<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GroupDrive;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class TravelSheetReport extends DeviceHistoryReport
{
    const TYPE_ID = 4;

    protected $disableFields = ['geofences', 'speed_limit'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.travel_sheet');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            Drivers::class,

            GroupDrive::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $rows[] = $this->getDataFromGroup($group, [
                'group_key',
                'start_at',
                'end_at',
                'duration',
                'distance',
                'drivers',
                'speed_max',
                'speed_avg',
                'location_start',
                'location_end',
                'fuel_consumption_list',
                'fuel_price_list'
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        $totals = parent::getTotals($group, ['drive_distance', 'drive_duration']);
        $totals['distance'] = $totals['drive_distance'];

        return $totals;
    }
}