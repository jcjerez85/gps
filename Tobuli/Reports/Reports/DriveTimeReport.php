<?php

namespace Tobuli\Reports\Reports;

use Illuminate\Support\Arr;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\FirstDrive;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupSingle;
use Tobuli\History\Actions\LastDrive;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;
use Formatter;

class DriveTimeReport extends DeviceHistoryReport
{
    const TYPE_ID = 72;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.drive_time');
    }

    protected function getActionsList()
    {
        return [
            Drivers::class,
            DriveStop::class,
            Duration::class,
            Distance::class,
            FirstDrive::class,
            LastDrive::class,

            GroupSingle::class,
            GroupDailySplit::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $row = $this->getDataFromGroup($group, [
                'timestamp',
                'drivers',
                'stop_duration',
                'drive_duration',
                'distance',
                'duration',
            ]);

            $_key = 'first_drive';
            $firstDrive = ($group->stats()->has($_key) && $group->stats()->get($_key)->get())
                ? $group->stats()->get($_key)->get()
                : null;

            $_key = 'last_drive';
            $lastDrive = ($group->stats()->has($_key) && $group->stats()->get($_key)->get())
                ? $group->stats()->get($_key)->get()
                : null;

            $row['start_date'] = $firstDrive ? Formatter::date()->human($firstDrive->time) : null;
            $row['start_time'] = $firstDrive ? Formatter::dtime()->human($firstDrive->time) : null;

            $row['stop_date'] = $firstDrive ? Formatter::date()->human($lastDrive->time) : null;
            $row['stop_time'] = $firstDrive ? Formatter::dtime()->human($lastDrive->time) : null;

            $_key = 'drive_duration';
            $durationDrive = ($group->stats()->has($_key) && $group->stats()->get($_key)->get())
                ? $group->stats()->get($_key)->get()
                : 0;

            $duration = ($firstDrive && $lastDrive)
                ? (strtotime($lastDrive->time) - strtotime($firstDrive->time))
                : 0;

            $durationStop = ($duration)
                ? $duration - $durationDrive
                : 0;

            $row['duration'] = Formatter::duration()->human($duration);
            $row['stop_duration'] = Formatter::duration()->human($durationStop);


            $rows[] = $row;
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