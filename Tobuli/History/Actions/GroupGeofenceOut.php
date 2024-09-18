<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Group;

class GroupGeofenceOut extends ActionGroupGeofence
{
    public function proccess($position)
    {
        $enters = $this->enterGeofences($position);
        foreach ($enters as $geofence_id)
        {
            $this->history->groupEnd("geofence_out_{$geofence_id}", $position);
        }

        $lefts = $this->leftGeofences($position);

        foreach ($lefts as $geofence_id)
        {
            $group = new Group("geofence_out_{$geofence_id}");
            $group->geofence_id = $geofence_id;
            $group->setLastClose(false);

            $this->history->groupStart($group, $position);
        }
    }
}