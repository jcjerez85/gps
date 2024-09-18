<?php

namespace Tobuli\Reports\Reports;

use Illuminate\Support\Arr;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class GeofencesInOutMergedReport extends DeviceHistoryReport
{
    const TYPE_ID = 71;

    protected $disableFields = ['speed_limit', 'stops'];
    protected $validation = ['geofences' => 'required'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_in_out_merged');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,

            GroupDailySplit::class,
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
            $rows[] = $this->getDataFromGroup($group, [
                'timestamp',
                'start_date_at',
                'start_time_at',
                'end_date_at',
                'end_time_at',
                'stop_duration',
                'drive_distance',
                'location',
                'group_geofence'
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return [];
    }

    protected function generate()
    {
        $this->getDevicesQuery()->chunk(1000, function ($devices) {
            foreach ($devices as $device) {
                $data = $this->generateDevice($device);

                if ($this->getSkipBlankResults() && empty($data))
                    continue;

                if (empty($data)) {
                    $this->items[] = [
                        'meta' => $this->getDeviceMeta($device),
                        'error' => trans('front.nothing_found_request')
                    ];

                    continue;
                }

                foreach ($data['table']['rows'] as $row) {
                    $this->items[] = [
                        'meta' => $this->getDeviceMeta($device),
                        'table' => [
                            'rows' => [$row]
                        ]
                    ];
                }
            }
        });
    }

    protected function afterGenerate()
    {
        if (empty($this->items))
            return;

        $this->items = Arr::sort($this->items, function($item) {
            return $item['table']['rows'][0]['timestamp'] ?? null;
        });
    }
}