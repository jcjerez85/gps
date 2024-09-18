<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Group;
use Tobuli\History\GroupContainer;
use Tobuli\Reports\DeviceHistoryReport;

class GeofencesInGroupReport extends DeviceHistoryReport
{
    const TYPE_ID = 57;

    protected $disableFields = ['speed_limit', 'stops', 'show_addresses', 'zones_instead'];
    protected $validation = ['geofences' => 'required'];

    private $totalGroups;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_in_grouped');
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

    protected function getTable($groups)
    {
        $rows = [];

        foreach ($groups->all() as $group)
        {
            $rows[] = $this->getDataFromGroup($group, [
                'duration',
                'distance',
                'location',
                'group_geofence'
            ]);
        }

        usort($rows, function ($row1, $row2) {
            return $row1['group_geofence'] <=> $row2['group_geofence'];
        });

        return [
            'rows'   => $rows,
            'totals' => $this->getDataFromGroup($groups->merge(), [
                'duration',
                'distance',
            ]),
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return [];
    }

    protected function afterGenerate()
    {
        if (empty($this->items))
            return;

        if (empty($this->totalGroups))
            return;

        $this->items[] = [
            'meta'   => [
                [
                    'title' => trans('global.total'),
                    'value' => trans('front.all_devices'),
                ]
            ],
            'table'  => $this->getTable($this->totalGroups),
            'totals' => []
        ];
    }

    protected function generateDevice($device)
    {
        if ($error = $this->precheckError($device))
            return [
                'meta' => $this->getDeviceMeta($device),
                'error' => $error
            ];

        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        $groups = $data['groups']->mergeByProperties(['geofence_id']);

        $this->mergeTotalGroups($data['groups']);

        return [
            'meta'   => $this->getDeviceMeta($device),
            'table'  => $this->getTable($groups),
            'totals' => []
        ];
    }

    protected function mergeTotalGroups(GroupContainer $groups)
    {
        if (is_null($this->totalGroups))
            $this->totalGroups = new GroupContainer();

        $this->totalGroups = $groups->mergeByProperties(['geofence_id'], $this->totalGroups);
    }
}