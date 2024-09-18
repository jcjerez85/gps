<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Group;

class GroupGeofenceInOut extends ActionGroupGeofence
{
    public function proccess($position)
    {
        $this->proccessIn($position);
        $this->proccessOut($position);
    }

    public function proccessIn($position)
    {
        $lefts = $this->leftGeofences($position);

        foreach ($lefts as $geofence_id)
        {
            $this->history->groupEnd("geofence_in", $position);
        }

        $enters = $this->enterGeofences($position);

        foreach ($enters as $geofence_id)
        {
            $group = new Group("geofence_in");
            $group->geofence_id = $geofence_id;
            $group->setLastClose(false);

            $this->history->groupStart($group, $position);
        }
    }

    public function proccessOut($position)
    {
        $enters = $this->enterGeofences($position);

        foreach ($enters as $geofence_id)
        {
            $this->history->groupEnd("geofence_out", $position);
        }

        if (! empty($enters)) {
            return;
        }

        $lefts = $this->leftGeofences($position);

        foreach ($lefts as $geofence_id)
        {
            $group = new Group("geofence_out");
            $group->geofence_id = $geofence_id;
            $group->setLastClose(false);

            $this->history->groupStart($group, $position);
        }
    }
}