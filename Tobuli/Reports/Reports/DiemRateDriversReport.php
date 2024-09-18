<?php

namespace Tobuli\Reports\Reports;

use Illuminate\Support\Arr;
use Tobuli\Entities\UserDriver;
use Tobuli\History\Actions\AppendDiemRateGeofencesOverwrite;
use Tobuli\History\Actions\DiemRate;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class DiemRateDriversReport extends DeviceHistoryReport
{
    const TYPE_ID = 74;

    protected $disableFields = ['speed_limit', 'stops', 'geofences'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.diem_rate_drivers');
    }

    public static function isEnabled()
    {
        return \Tobuli\Entities\DiemRate::active()->count();
    }

    protected function getActionsList()
    {
        return [
            AppendDiemRateGeofencesOverwrite::class,
            Duration::class,
            Distance::class,
            DiemRate::class,
            Drivers::class,

            GroupGeofenceIn::class,
        ];
    }

    protected function afterGenerate()
    {
        if (empty($this->items))
            return;

        foreach ($this->items as $driver_id => & $item)
        {
            $item['table']['totals'] = $this->getDataFromGroup($item['container'], [
                'duration',
                'distance',
                'diem_rate'
            ]);
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

        return array_merge($deviceMeta, $this->getDataFromGroup($group, [
            'start_at',
            'end_at',
            'duration',
            'distance',
            'location',
            'group_geofence',
            'diem_rate'
        ]));
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