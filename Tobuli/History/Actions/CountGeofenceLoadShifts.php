<?php

namespace Tobuli\History\Actions;

use DateTime;
use Formatter;
use Tobuli\History\Group;
use Tobuli\History\Stats\StatCount;

class CountGeofenceLoadShifts extends ActionStat
{
    use LoadTrait;

    private static $shifts = null;

    private static function resolveShifts(array $parameters)
    {
        static::$shifts = [];

        foreach ($parameters as $key => $time) {
            $key = explode('shift_', $key);

            if (count($key) !== 2) {
                continue;
            }

            $key = $key[1];
            $meta = explode('_', $key);

            if ($meta[0] === 'start' || $meta[0] === 'finish') {
                $time = Formatter::time()->reverse($time, 'H:i');
                static::$shifts[$meta[1]][$meta[0]] = DateTime::createFromFormat('!H:i', $time);
            }
        }

        foreach (static::$shifts as $key => &$shift) {
            if (empty($shift['start']) || empty($shift['finish'])) {
                unset(static::$shifts[$key]);

                continue;
            }

            if ($shift['start'] > $shift['finish']) {
                $shift['finish']->modify('+1 day');
            }

            $shift['name'] = $shift['start']->format('H:i') . ' - ' . $shift['finish']->format('H:i');
        }
    }

    public static function getShifts()
    {
        return self::$shifts;
    }

    public static function required()
    {
        return [
            AppendLoadChangeIfHasGeofences::class,
        ];
    }

    public function boot()
    {
        static::resolveShifts($this->history->allConfig());

        foreach (static::$shifts as $shift) {
            $group = new Group($shift['name']);

            foreach ($this->history->getGeofences() as $geofence) {
                foreach (static::$loadStates as $state) {
                    $key = $this->getStatName($geofence->id, $state);

                    $group->stats()->set($key, new StatCount());
                }
            }

            $this->history->groups()->add($group);
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

        foreach (static::$shifts as $shift) {
            if (!$this->isPositionInShift($position, $shift)) {
                continue;
            }

            foreach ($position->geofences as $geofenceId) {
                $statKey = $this->getStatName($geofenceId, $position->loadChange['state']);

                $this->history->groups()->applyStatOnGroup($shift['name'], $statKey, 1);
            }
        }
    }

    private function isPositionInShift($position, $shift): bool
    {
        $loadTime = DateTime::createFromFormat('!H:i', (new DateTime($position->loadChange['time']))->format('H:i'));
        $loadTimeNextDay = (clone $loadTime)->modify('+1 day');

        return ($shift['start'] <= $loadTime && $loadTime <= $shift['finish'])
            || ($shift['start'] <= $loadTimeNextDay && $loadTimeNextDay <= $shift['finish']);
    }
}