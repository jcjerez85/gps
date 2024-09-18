<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\AppendSpeedLimitRoads;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupOverspeed;
use Tobuli\History\Actions\Overspeed;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class OverspeedsRoadsReport extends DeviceHistoryReport
{
    const TYPE_ID = 59;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];

    public function getInputParameters(): array
    {
        return [
            \Field::number('speed_limit_tolerance', trans('validation.attributes.speed_limit_tolerance') . ' (' . trans('front.kph') . ')')
                ->setValidation('numeric')
            ,
        ];
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.overspeeds') . ' / ' . trans('front.road');
    }

    static public function isEnabled()
    {
        return !empty(config('services.speedlimit.key'));
    }

    protected function getActionsList()
    {
        return [
            AppendSpeedLimitRoads::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Overspeed::class,

            GroupOverspeed::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $rows[] = $this->getDataFromGroup($group, [
                'start_at',
                'end_at',
                'duration',
                'speed_max',
                'speed_avg',
                'location',
                'overspeed_limit'
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }
}