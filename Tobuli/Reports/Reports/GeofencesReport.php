<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class GeofencesReport extends DeviceHistoryReport
{
    const TYPE_ID = 53;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofences');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            Speed::class,
            GeofencesIn::class,

            GroupGeofenceIn::class,
        ];
    }

    protected function isEmptyResult($data)
    {
        return empty($data['groups']) || empty($data['groups']->all());
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $row = $this->getDataFromGroup($group, [
                'start_at',
                'end_at',
                'duration',
                'distance',
                'speed_avg',
                'speed_max',
                'group_geofence'
            ]);

            $distance = $group->stats()->get('distance')->value();
            $duration = $group->stats()->get('duration')->value();
            $speed = empty($duration) ? 0 : ($distance) / ($duration / 3600);

            $row['speed_avg_calc'] = Formatter::speed()->human($speed);

            $rows[] = $row;
        }

        $groups = $data['groups']->merge();

        $totals = $this->getDataFromGroup($groups, [
            'speed_avg',
            'speed_max',
            'duration',
            'distance',
        ]);

        $distance = $groups->stats()->get('distance')->value();
        $duration = $groups->stats()->get('duration')->value();
        $speed = empty($duration) ? 0 : ($distance) / ($duration / 3600);

        $totals['speed_avg_calc'] = Formatter::speed()->human($speed);

        return [
            'rows'   => $rows,
            'totals' => $totals,
        ];
    }
}