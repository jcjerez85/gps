<?php

namespace Tobuli\Reports\Reports;

use Illuminate\Support\Arr;
use Tobuli\Entities\UserDriver;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GroupDriveStop;
use Tobuli\History\Actions\Odometer;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class DrivesStopsDriversReport extends DeviceHistoryReport
{
    const TYPE_ID = 19;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.drives_and_stops').' / '.trans('front.drivers');
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
            Odometer::class,

            GroupDriveStop::class,
        ];

        return $list;
    }

    protected function afterGenerate()
    {
        if (empty($this->items))
            return;

        foreach ($this->items as $driver_id => & $item)
        {
            $item['totals'] = $this->getTotals($item['container']);
            unset($this->items[$driver_id]['container']);
        }
    }

    protected function generate()
    {
        $this->getDevicesQuery()->chunk(1000, function ($devices) {
            foreach ($devices as $device) {
                $this->generateDevice($device);
            }
        });
    }

    protected function groupToRow($group, $device)
    {
        $deviceMeta = Arr::pluck($this->getDeviceMeta($device), 'value', 'key');

        return array_merge($deviceMeta, [
            'group_key'   => $group->getKey(),
            'status'      => $group->getKey() == 'drive' ? trans('front.moving') : trans('front.stopped'),
            'start_at'    => $group->getStartAt(),
            'end_at'      => $group->getEndAt(),
            'duration'    => $group->stats()->human('duration'),
            'distance'    => $group->stats()->human('distance'),
            'engine_idle' => $group->stats()->human('engine_idle'),
            'drivers'     => $group->stats()->human('drivers'),
            'speed_max'   => $group->stats()->human('speed_max'),
            'speed_avg'   => $group->stats()->human('speed_avg'),
            'location'    => $group->getKey() == 'stop' ? $this->getLocation($group->getStartPosition()) : null,
            'fuel_consumption' => $group->stats()->human('fuel_consumption'),
            'address'     => $this->getAddress($group->getStartPosition()),
        ]);
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        foreach ($data['groups']->all() as $group)
        {
            $drivers = $group->stats()->get('drivers')->get();
            $driver_id = empty($drivers) ? 0 : $drivers[0];

            if (empty($this->items[$driver_id])) {
                $this->items[$driver_id] = [
                    'meta' => [
                        [
                            'key'   => 'driver_name',
                            'title' => trans('front.driver'),
                            'value' => runCacheEntity(UserDriver::class, $driver_id)
                                ->implode('name_with_rfid', ', ')
                        ]
                    ],
                    'table' => [
                        'rows' => [],
                    ],
                    'container' => new Group('container')
                ];
            }

            $this->items[$driver_id]['container']->applyArray($group->stats()->all());
            $this->items[$driver_id]['table']['rows'][] = $this->groupToRow($group, $device);
        }
    }
}