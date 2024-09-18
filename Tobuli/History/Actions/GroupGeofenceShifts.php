<?php

namespace Tobuli\History\Actions;

use DateTime;
use Formatter;

class GroupGeofenceShifts extends ActionGroup
{
    /**
     * @var null|array
     */
    private static $shifts = null;

    private $openGroups = [];

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
                $timeRev = Formatter::time()->reverse($time, 'H:i');
                static::$shifts[$meta[1]][$meta[0]] = DateTime::createFromFormat('!H:i', $timeRev);
                static::$shifts[$meta[1]][$meta[0] . '_orig'] = $time;
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

            $shift['name'] = $shift['start_orig'] . ' - ' . $shift['finish_orig'];
        }
    }

    public static function getShifts()
    {
        return self::$shifts;
    }

    public static function required()
    {
        return [AppendGeofences::class];
    }

    public function boot()
    {
        static::resolveShifts($this->history->allConfig());
    }

    public function proccess($position)
    {
        $loadTime = DateTime::createFromFormat('!H:i', (new DateTime($position->time))->format('H:i'));

        foreach ($this->openGroups as &$value) {
            $value = false;
        }

        foreach ($position->geofences as $geofenceId) {
            foreach (static::$shifts as $shift) {
                $positionInShift = $this->isPositionInShift($loadTime, $shift);
                $groupName = $this->getGroupName($shift, $geofenceId);
                $groupOpen = isset($this->openGroups[$groupName]);

                if ($positionInShift && $groupOpen) {
                    $this->openGroups[$groupName] = true;
                }

                if ($positionInShift && !$groupOpen) {
                    $this->history->groupStart($groupName, $position);

                    $this->openGroups[$groupName] = true;
                }
            }
        }

        foreach ($this->openGroups as $groupName => $used) {
            if (!$used) {
                $this->history->groupEnd($groupName, $position);
            }
        }

        $this->openGroups = array_filter($this->openGroups);
    }

    private function isPositionInShift($loadTime, $shift): bool
    {
        $loadTimeNextDay = (clone $loadTime)->modify('+1 day');

        return ($shift['start'] <= $loadTime && $loadTime <= $shift['finish'])
            || ($shift['start'] <= $loadTimeNextDay && $loadTimeNextDay <= $shift['finish']);
    }

    protected function getGroupName($shift, $geofenceId): string
    {
        return 'shift_' . $shift['name'] . '_geofence_' . $geofenceId;
    }

    public static function getShiftFromGroupName(string $groupName): string
    {
        $groupName = explode('shift_', $groupName)[1];

        return explode('_geofence_', $groupName)[0];
    }

    public static function getGeofenceFromGroupName(string $groupName): string
    {
        return explode('_geofence_', $groupName)[1];
    }
}