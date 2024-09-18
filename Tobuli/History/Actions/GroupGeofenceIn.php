<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Group;

class GroupGeofenceIn extends ActionGroupGeofence
{
    public function proccess($position)
    {
        $lefts = $this->leftGeofences($position);

        foreach ($lefts as $geofence_id)
        {
            $this->history->groupEnd("geofence_in_{$geofence_id}", $position);
        }

        $enters = $this->enterGeofences($position);

        foreach ($enters as $geofence_id)
        {
            $group = new Group("geofence_in_{$geofence_id}");
            $group->geofence_id = $geofence_id;
            $group->setLastClose(false);

            $this->history->groupStart($group, $position);
        }
    }
}