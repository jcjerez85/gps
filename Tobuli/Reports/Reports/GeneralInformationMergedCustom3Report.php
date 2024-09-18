<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\FirstDrive;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupSingle;
use Tobuli\History\Actions\LastDrive;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationMergedCustom3Report extends DeviceHistoryReport
{
    const TYPE_ID = 49;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.general_information_merged_custom_3');
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
            Fuel::class,

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
                'fuel_level_start_list',
                'fuel_level_end_list',
                'fuel_avg_list',
                'fuel_consumption_list',
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => $this->getDataFromGroup($data['root'], [
                'stop_duration',
                'engine_idle',
                'engine_work',
                'engine_hours',
                'drive_duration',
                'overspeed_count',
                'distance',
                'fuel_level_start_list',
                'fuel_level_end_list',
                'fuel_avg_list',
                'fuel_consumption_list'
            ])
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return [];
    }
}