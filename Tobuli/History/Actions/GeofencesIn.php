<?php

namespace Tobuli\History\Actions;

use Tobuli\Entities\Geofence;
use Tobuli\History\Stats\StatModelList;

class GeofencesIn extends ActionStat
{
    static public function required()
    {
        return [
            AppendGeofences::class
        ];
    }

    public function boot()
    {
        $this->registerStat('geofences_in', (new StatModelList(Geofence::class)));
    }

    public function proccess($position)
    {
        if (empty($position->geofences))
            return;

        foreach ($position->geofences as $geofence_id)
            $this->history->applyStat('geofences_in', $geofence_id);

    }
}