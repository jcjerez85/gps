<?php

namespace Tobuli\History\Actions;

class GroupGeofenceGroupShifts extends GroupGeofenceShifts
{
    protected function getGroupName($shift, $geofenceId): string
    {
        return 'shift_' . $shift['name'] . '_geofence_' . $this->history->getGeofences()->find($geofenceId)->group_id;
    }
}