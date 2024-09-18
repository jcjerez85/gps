<?php

namespace Tobuli\History\Actions;

use Tobuli\Entities\Geofence;
use Tobuli\History\Stats\StatCount;
use Tobuli\History\Stats\StatModelList;

class GeofencesCount extends ActionStat
{
    static public function required()
    {
        return [
            AppendGeofences::class
        ];
    }

    public function boot()
    {
        $this->registerStat('geofences_in_count', new StatCount());
        $this->registerStat('geofences_out_count', new StatCount());
    }

    public function proccess($position)
    {
        $previous = $this->getPrevPosition();

        if ( ! $previous)
            return;

        $enters = array_diff($this->geofences($position), $this->geofences($previous));
        $lefts  = array_diff($this->geofences($previous), $this->geofences($position));

        $this->history->applyStat('geofences_in_count', count($enters));
        $this->history->applyStat('geofences_out_count', count($lefts));
    }

    protected function geofences($position)
    {
        if (empty($position->geofences))
            return [];

        return $position->geofences;
    }
}