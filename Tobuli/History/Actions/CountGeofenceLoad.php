<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatCount;

class CountGeofenceLoad extends ActionStat
{
    use LoadTrait;

    static public function required()
    {
        return [
            AppendLoadChangeIfHasGeofences::class,
        ];
    }

    public function boot()
    {
        foreach ($this->history->getGeofences() as $geofence) {
            foreach (static::$loadStates as $state) {
                $key = $this->getStatName($geofence->id, $state);

                $this->history->registerStat($key, new StatCount());
            }
        }
    }

    private function getStatName(int $geofenceId, int $state): string
    {
        return $this->getLoadStateName($state) . '_count_geofence_' . $geofenceId;
    }

    public function proccess($position)
    {
        if (!$this->isPositionLoadValid($position)) {
            return;
        }

        foreach ($position->geofences as $geofenceId) {
            $statKey = $this->getStatName($geofenceId, $position->loadChange['state']);

            $this->history->applyStat($statKey, 1);
        }
    }
}