<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\AppendDiemRateGeofencesOverwrite;
use Tobuli\History\Actions\DiemRate;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class DiemRateDevicesReport extends DeviceHistoryReport
{
    const TYPE_ID = 73;

    protected $disableFields = ['speed_limit', 'stops', 'geofences'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.diem_rate_devices');
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
                'start_at',
                'end_at',
                'duration',
                'distance',
                'location',
                'group_geofence',
                'diem_rate'
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => $this->getDataFromGroup($data['groups']->merge(), [
                'duration',
                'distance',
                'diem_rate'
            ]),
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return [];
    }
}