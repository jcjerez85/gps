<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\FirstDrive;
use Tobuli\History\Actions\GroupDaily;
use Tobuli\History\Actions\LastDrive;
use Tobuli\Reports\DeviceHistoryReport;

class WorkHoursDailyReport extends DeviceHistoryReport
{
    const TYPE_ID = 48;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];

    private $current_date;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.work_hours_daily');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Distance::class,
            FirstDrive::class,
            LastDrive::class,

            GroupDaily::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $data = $this->getDataFromGroup($group, [
                'drive_distance',
                'drive_duration',
                'last_drive_time',
                'first_drive_time',
                'distance',
                'date',
            ]);

            $last  = $group->stats()->get('last_drive')->get();
            $first = $group->stats()->get('first_drive')->get();

            $data['duration'] = Formatter::duration()->human(
                strtotime($last->time ?? null) - strtotime($first->time ?? null)
            );

            $rows[] = $data;
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

}