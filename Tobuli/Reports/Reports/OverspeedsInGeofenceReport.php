<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\AppendOverspeedingProcessOnly;
use Tobuli\History\Actions\AppendOverspeedOnlyInGeofences;
use Tobuli\History\Actions\BreakOverspeed;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\History\Actions\GroupOverspeed;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class OverspeedsInGeofenceReport extends DeviceHistoryReport
{
    const TYPE_ID = 47;

    protected $disableFields = ['stops'];
    protected $validation = [
        'geofences' => 'required',
        'speed_limit' => 'required',
    ];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return  trans('front.overspeeds') . ' / ' . trans('front.geofences');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            Speed::class,
            OverspeedStatic::class,
            GeofencesIn::class,
            Drivers::class,
            AppendOverspeedingProcessOnly::class,
            AppendOverspeedOnlyInGeofences::class,

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
                'geofences_in',
                'drivers'
            ]);

            $this->group->applyArray($group->stats()->only([
                'duration',
                'speed_max',
                'speed_avg',
            ]));
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function isEmptyResult($data)
    {
        return empty($data['groups']) || empty($data['groups']->all());
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'table'  => $this->getTable($data),
            'totals' => $this->getTotals($data['groups']->merge())
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return parent::getTotals($group, ['overspeed_count']);
    }
}