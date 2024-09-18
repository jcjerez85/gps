<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\FirstDrive;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupSingle;
use Tobuli\History\Actions\LastDrive;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationMergedCustomReport extends DeviceHistoryReport
{
    const TYPE_ID = 16;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.general_information_merged_custom');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            EngineHours::class,
            OverspeedStatic::class,
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
            $rows[] = $this->getDataFromGroup($group, [
                'date',
                'stop_duration',
                'engine_idle',
                'engine_work',
                'engine_hours',
                'drive_duration',
                'overspeed_count',
                'distance',
                'first_drive_time',
                'last_drive_time',
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
}