<?php

namespace Tobuli\History\Actions;

class AppendFuelChange extends ActionAppend
{
    const DURATION_CHANGE = 5*60;
    const INCREASING = true;
    const DECREASING = false;

    protected $sensors = [];

    static public function required()
    {
        return [
            AppendFuelTanks::class,
            AppendDuration::class,
            AppendMoveState::class,
        ];
    }

    public function boot()
    {
        $device = $this->getDevice();

        $this->sensors = $device->sensors
            ->filter(function($sensor) {
                return in_array($sensor->type, ['fuel_tank', 'fuel_tank_calibration']);
            });
    }

    public function proccess(& $position)
    {
        $position->fuel_change = null;

        $prevPosition = $this->getPrevPosition();

        if (!$prevPosition)
            return null;

        foreach ($this->sensors as $sensor)
            $this->proccessSensor($sensor, $position);
    }

    protected function proccessSensor($sensor, & $position)
    {
        $prevPosition = $this->getPrevPosition();

        $value = $position->fuel_tanks[$sensor->id];
        $prevValue = $prevPosition->fuel_tanks[$sensor->id];

        if (empty($value) || empty($prevValue))
            return;

        $diff = $value - $prevValue;

        $status = $diff > 0 ? self::INCREASING : self::DECREASING;

        $change = $prevPosition->fuel_change[$sensor->id] ?? null;

        if (isset($change['end']))
            $change = null;

        if ($diff == 0) {
            if ($change && ($position->moving || ($position->timestamp - $change['last']->timestamp) > self::DURATION_CHANGE))
                $change['end'] = $prevPosition;

            $position->fuel_change[$sensor->id] = $change;
            return;
        }

        if (is_null($change))
            $change = [
                'sensor_id' => $sensor->id,
                'status' => $status,
                'start' => $prevPosition,
                'diff' => 0
            ];

        if ($change['status'] === $status) {
            $change['diff'] += $diff;
        } elseif(abs($change['diff']) < abs($diff)) {
            $change = [
                'sensor_id' => $sensor->id,
                'status' => $status,
                'start' => $prevPosition,
                'diff' => $diff
            ];
        } else {
            $change['end'] = $prevPosition;
        }

        $change['last'] = $prevPosition;
        $position->fuel_change[$sensor->id] = $change;
    }
}