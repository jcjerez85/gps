<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class GeofencesInOutReport extends DeviceHistoryReport
{
    const TYPE_ID = 7;

    protected $disableFields = ['speed_limit', 'stops'];
    protected $validation = ['geofences' => 'required'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_in_out');
    }

    protected function getActionsList()
    {
        return [
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
                'group_geofence'
            ]);
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