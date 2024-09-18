<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\GroupGeofenceShifts;
use Tobuli\History\Group;

class GeofencesStopShiftReport extends AbstractGeofencesStopReport
{
    const TYPE_ID = 68;

    public array $table = [];
    public array $tableTotals = [];
    public array $columns = [];

    public function getInputParameters(): array
    {
        $timeSelectOptions = ['' => trans('admin.select')] + getSelectTimeRange();
        $inputParameters = [];

        for ($i = 1; $i <= 3; $i++) {
            $inputParameters[] = \Field::select(
                'shift_start_' . $i,
                trans('validation.attributes.shift_start') . " #$i"
            )
                ->setOptions($timeSelectOptions)
                ->setValidation('date_format:H:i');

            $inputParameters[] = \Field::select(
                'shift_finish_' . $i,
                trans('validation.attributes.shift_finish') . " #$i"
            )
                ->setOptions($timeSelectOptions)
                ->setValidation('date_format:H:i');
        }

        return array_merge($inputParameters, parent::getInputParameters());
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_stop_count_shift');
    }

    protected function afterGenerate()
    {
        $this->tableTotals['shift_name'] = trans('global.total');

        foreach ($this->table as $name => &$shift) {
            foreach ($shift as $key => $value) {
                if (!isset($this->tableTotals[$key])) {
                    $this->tableTotals[$key] = 0;
                }
                
                $this->tableTotals[$key] += $value;
            }

            $shift = ['shift_name' => $name] + $shift;
        }

        $this->columns['shift_name'] = trans('front.shift_time');

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

        if (empty($this->table)) {
            foreach (GroupGeofenceShifts::getShifts() as $shift) {
                $this->table[$shift['name']] = [
                    'total' => 0,
                ];
            }
        }

        /** @var Group $group */
        foreach ($data['groups']->all() as $group) {
            $shiftName = GroupGeofenceShifts::getShiftFromGroupName($group->getKey());
            $geofence = GroupGeofenceShifts::getGeofenceFromGroupName($group->getKey());

            if (empty($this->table[$shiftName])) {
                continue;
            }

            if (!isset($this->table[$shiftName][$geofence])) {
                $this->table[$shiftName][$geofence] = 0;
            }

            if ($this->isGroupCalculated($group)) {
                $this->table[$shiftName][$geofence]++;
                $this->table[$shiftName]['total']++;
            }
        }
    }
}