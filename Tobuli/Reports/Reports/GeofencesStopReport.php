<?php

namespace Tobuli\Reports\Reports;

use Illuminate\Support\Arr;
use Tobuli\History\Actions\GroupGeofenceShifts;
use Tobuli\History\Group;

class GeofencesStopReport extends AbstractGeofencesStopReport
{
    const TYPE_ID = 67;

    public array $tableTotals = [];
    public array $columns = [];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_stop_count');
    }

    protected function beforeGenerate()
    {
        parent::beforeGenerate();

        $this->parameters['shift_start_1'] = '00:00';
        $this->parameters['shift_finish_1'] = '23:59';
    }

    protected function afterGenerate()
    {
        $this->tableTotals['device_name'] = trans('global.total');

        foreach ($this->items as $deviceData) {
            $deviceData = $deviceData['table'];

            foreach ($deviceData as $key => $value) {
                if (!isset($this->tableTotals[$key])) {
                    $this->tableTotals[$key] = 0;
                }

                $this->tableTotals[$key] += $value;
            }
        }

        if ($this->parameters['group_geofences']) {
            foreach ($this->geofences as $geofence) {
                if ($group = $geofence->group) {
                    $this->columns[$group->id] = $group->title;
                } else {
                    $this->columns[0] = trans('front.ungrouped');
                }
            }
        } else {
            foreach ($this->getGeofences() as $geofence) {
                $this->columns[$geofence->id] = $geofence->name;
            }
        }

        ksort($this->columns);

        $this->columns['total'] = trans('global.total');
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        $table = ['total' => 0];

        $shift = Arr::first(GroupGeofenceShifts::getShifts());

        /** @var Group $group */
        foreach ($data['groups']->all() as $group) {
            $shiftName = GroupGeofenceShifts::getShiftFromGroupName($group->getKey());
            $geofence = GroupGeofenceShifts::getGeofenceFromGroupName($group->getKey());

            if ($shiftName !== $shift['name']) {
                continue;
            }

            if (!isset($table[$geofence])) {
                $table[$geofence] = 0;
            }

            if ($this->isGroupCalculated($group)) {
                $table[$geofence]++;
                $table['total']++;
            }
        }

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'table' => $table,
        ];
    }
}