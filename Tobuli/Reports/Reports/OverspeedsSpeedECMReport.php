<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\AppendSpeedECM;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupOverspeed;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Actions\SpeedGPS;
use Tobuli\History\Actions\Tachometer;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class OverspeedsSpeedECMReport extends DeviceHistoryReport
{
    const TYPE_ID = 52;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.overspeeds_speed_ecm');
    }

    public static function isEnabled()
    {
        return settings('plugins.speed_compare_gps_ecm_report.status');
    }

    protected function getActionsList()
    {
        return [
            AppendSpeedECM::class,
            Duration::class,
            Distance::class,
            Speed::class,
            SpeedGPS::class,
            Tachometer::class,
            OverspeedStatic::class,

            GroupOverspeed::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $speed_max = $group->stats()->value('speed_max');
            $speed_gps_max = $group->stats()->value('speed_gps_max');

            if (abs($speed_max - $speed_gps_max) > 20)
                continue;

            $rows[] = $this->getDataFromGroup($group, [
                'start_at',
                'end_at',
                'duration',
                'speed_max',
                'speed_avg',
                'speed_gps_max',
                'tachometer',
                'location',
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return parent::getTotals($group, ['overspeed_count']);
    }

    protected function precheckError($device) {
        if ($device->getSensorByType('speed_ecm'))
            return null;

        return dontExist('front.sensor');
    }
}