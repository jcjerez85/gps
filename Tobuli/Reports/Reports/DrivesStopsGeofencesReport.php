<?php

namespace Tobuli\Reports\Reports;

use Tobuli\Entities\Geofence;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\GroupDriveStop;
use Tobuli\History\Actions\GroupGeofenceInOut;
use Tobuli\History\Actions\OdometersDiff;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class DrivesStopsGeofencesReport extends DeviceHistoryReport
{
    const TYPE_ID = 18;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.drives_and_stops').' / '.trans('front.geofences');
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

            GroupDriveStop::class,
            GroupGeofenceInOut::class,
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
            if (in_array($group->getKey(), ['drive', 'stop'])) {
                $rows[] = [
                    'group_key'        => $group->getKey(),
                    'status'           => $group->getKey() == 'drive' ? trans('front.moving') : trans('front.stopped'),
                    'start_at'         => $group->getStartAt(),
                    'end_at'           => $group->getEndAt(),
                    'duration'         => $group->stats()->human('duration'),
                    'distance'         => $group->stats()->human('distance'),
                    'engine_idle'      => $group->stats()->human('engine_idle'),
                    'drivers'          => $group->stats()->human('drivers'),
                    'speed_max'        => $group->stats()->human('speed_max'),
                    'speed_avg'        => $group->stats()->human('speed_avg'),
                    'location'         => $group->getKey() == 'stop' ? $this->getLocation($group->getStartPosition()) : null,
                    'fuel_consumption' => $group->stats()->human('fuel_consumption'),
                    'geofences_in'     => $group->stats()->human('geofences_in')
                ];
            } else {
                $rows[] = [
                    'group_key'        => $group->getKey(),
                    'status'           => $group->getKey() == 'geofence_in' ? trans('front.zone_in') : trans('front.zone_out'),
                    'start_at'         => $group->getStartAt(),
                    'end_at'           => null,
                    'duration'         => null,
                    'distance'         => null,
                    'engine_idle'      => null,
                    'drivers'          => null,
                    'speed_max'        => null,
                    'speed_avg'        => null,
                    'fuel_consumption' => $group->stats()->human('fuel_consumption'),
                    'location'         => $location = $this->getLocation(
                        $group->getStartPosition(),
                        runCacheEntity(Geofence::class, $group->geofence_id)->implode('name', ', ')
                    ),
                ];
            }
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }
}