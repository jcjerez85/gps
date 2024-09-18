<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupEngineStatus;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class IgnitionOnOff24ModeReport extends DeviceHistoryReport
{
    const TYPE_ID = 30;

    protected $disableFields = ['speed_limit', 'stops', 'zones_instead', 'geofences'];

    public function getInputParameters(): array
    {
        $min = trans('front.minute_short');
        $h = trans('front.hour_short');
        
        return [
            \Field::select('ignition_off', trans('front.ignition_off'), 1)
                ->setOptions([
                    '0' => '> 0 ' . $min,
                    '1' => '> 1 ' . $min,
                    '2' => '> 2 ' . $min,
                    '5' => '> 5 ' . $min,
                    '10' => '> 10 ' . $min,
                    '20' => '> 20 ' . $min,
                    '30' => '> 30 ' . $min,
                    '60' => '> 1 ' . $h,
                    '120' => '> 2 ' . $h,
                    '300' => '> 5 ' . $h,
                ])
                ->setRequired()
                ->addValidation('integer')
            ,
        ];
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.ignition_on_off');
    }

    protected function getActionsList()
    {
        return [
            Speed::class,
            Distance::class,
            Duration::class,
            Drivers::class,

            GroupDailySplit::class,
            GroupEngineStatus::class,
        ];
    }

    protected function isEmptyResult($data)
    {
        return empty($data['groups']) || empty($data['groups']->all());
    }

    protected function getTable($data)
    {
        $current_date = null;

        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $datetime = $group->getStartPosition()->time;

            $date = Formatter::date()->convert($datetime);
            $time = Formatter::dtime()->convert($datetime);

            if ($current_date != $date)
            {
                $rows[] = [
                    'group_key' => 'date',
                    'date' => $date,
                ];

                $current_date = $date;
            }

            $row = $this->getDataFromGroup($group, [
                'group_key',
                'duration',
                'distance',
                'speed_avg',
                'drivers',
                'location',
            ]);

            $row['time'] = $time;

            $rows[] = $row;
        }

        return [
            'rows'   => $rows,
            'totals' => $this->getDataFromGroup($data['groups']->merge(), [
                'duration',
                'distance',
            ]),
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return [];
    }
}