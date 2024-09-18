<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupOverspeed;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Actions\Drivers;
use Tobuli\Reports\DeviceHistoryReport;
use Tobuli\Entities\UserDriver;

class SpeedReport extends DeviceHistoryReport
{
    const TYPE_ID = 45;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.speed_report');
    }

    public static function isEnabled() {
        return settings('plugins.speed_report.status');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            Speed::class,
            OverspeedStatic::class,
            Drivers::class,

            GroupOverspeed::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $row = $this->getDataFromGroup($group, [
                'start_at',
                'location',
                'speed_max',
                'overspeed_duration'
            ]);

            $drivers = $group->stats()->get('drivers')->get();
            $driver  = runCacheEntity(UserDriver::class, $drivers)->first();

            $row['driver'] = $driver ? $driver->name : '';
            $row['phone'] = $driver ? $driver->phone : '';
            $row['description'] = $driver ? $driver->description : '';

            $rows[] = $row;
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function isEmptyResult($data)
    {
        return empty($data['groups']) || empty($data['groups']->all());
    }
}
