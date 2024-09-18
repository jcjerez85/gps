<?php

namespace App\Jobs;

use Carbon\Carbon;
use Exception;
use Formatter;
use Tobuli\Entities\Event;
use Tobuli\Entities\User;

class ConfirmFuelLevelChange extends AbstractConfirmFuelLevel
{
    const PREHISTORY_DURATION = 15 * 60;
    const SECONDS_MAX_DURATION = 3600;
    const DIFF_MIN_PERCENT = 3;

    private $idleDuration;
    private $maxIdleDuration;

    public function __construct(
        $processKey,
        $time,
        array $eventData,
        array $mainFuel,
        int $iteration = 1,
        int $idleDuration = 0
    ) {
        $this->idleDuration = $idleDuration;
        $this->maxIdleDuration = settings('main_settings.default_object_online_timeout') * 60;

        parent::__construct($processKey, $time, $eventData, $mainFuel, $iteration);
    }

    /**
     *             /    | same as 2
     *       -----      | 3. if sensor sent no positions in this period - prolong check; else - finalize
     *     /            | same as 2
     *   /              | 2. if change is larger than X percent - continue, else - finalize
     * /                | 1. if change is larger than X percent - save initial stats and continue, else - finalize
     * @return bool
     */
    public function handle()
    {
        $processDuration = self::SECONDS_GAP * $this->iteration;

        if ($processDuration > self::SECONDS_MAX_DURATION || $this->idleDuration > $this->maxIdleDuration) {
            return $this->finalize();
        }

        try {
            $diff = $this->getFuelDifference();
        } catch (Exception $e) {
            $diff = null;
        }

        if ($diff && $diff['increased'] !== $this->mainFuel['increased']) {
            return $this->finalize($diff);
        }

        if (($this->iteration < 2 || !$this->mainFuel['increased'])
            && $diff
            && abs($diff['percent']) < self::DIFF_MIN_PERCENT
        ) {
            return $this->finalize($diff);
        }

        $this->applyIterationDiff($diff);

        return $this->prolongCheck();
    }

    private function finalize($diff = null): bool
    {
        $this->processManager->unlock($this->processKey);

        $prehistoryValues = $this->applyPrehistoryStats();

//        if (!$this->isIntervalValid($prehistoryValues)) {
//            return false;
//        }

        $totalDifference = $this->mainFuel['edge_value'] - $this->mainFuel['first_value'];
        $minDifferance = $totalDifference > 0 ? $this->getMinFillingDifference() : $this->getMinTheftDifference();

        if (abs($totalDifference) < $minDifferance) {
            return false;
        }

        if (!($user = User::find($this->eventData['user_id']))){
            return false;
        }

        Formatter::byUser($user);

        $this->eventData = array_merge($this->eventData, [
            'type'       => $totalDifference > 0 ? Event::TYPE_FUEL_FILL : Event::TYPE_FUEL_THEFT,
            'message'    => $totalDifference > 0 ? trans('front.fuel_fillings') : trans('front.fuel_thefts'),
        ]);
        $this->eventData['additional']['difference'] = round(abs($totalDifference));

        // if there was large change after direction switch, check for possible spike
        if (null && $diff && $diff['end_direction_changed']) {
            dispatch(
                (new ConfirmFuelLevelFuture(
                    $this->processKey,
                    $this->initTime,
                    $this->eventData,
                    $this->mainFuel,
                    ++$this->iteration
                ))->delay(self::FINALIZATION_GAP)
            );

            return true;
        }

        return $this->sendEvent();
    }

    /**
     * @throws Exception
     */
    private function getFuelDifference()
    {
        $timeFrom = Carbon::parse($this->initTime)->addSeconds(self::SECONDS_GAP * ($this->iteration - 1));
        $timeTo   = Carbon::parse($this->initTime)->addSeconds(self::SECONDS_GAP * $this->iteration);

        if ($this->idleDuration) {
            $timeFrom->subSeconds($this->idleDuration);
        }

        $positions = $this->device->positions()
            ->where('time', '>', $timeFrom)
            ->where('time', '<=', $timeTo)
            ->orderliness('ASC')
            ->get();

        if ($positions->count() < 1) {
            return null;
        }

        $diff = getSensorFuelDifference($this->deviceSensor, $positions, $this->mainFuel['last_value'] ?? null);

        $diff['end_direction_changed'] = $this->getSizeDiff($diff['edge_value'], $diff['last_value']) > 0.3;

        return $diff;
    }

    private function applyPrehistoryStats(): array
    {
        $increased = $this->mainFuel['increased'];

        $initTime = Carbon::parse($this->initTime);
        $prehistoryStart = (clone $initTime)->subSeconds(self::PREHISTORY_DURATION);

        $prevPositions = $this->device->positions()
            ->where('time', '<', $initTime)
            ->where('time', '>', $prehistoryStart)
            ->orderliness()
            ->cursor();

        $nextValue = $this->mainFuel['first_value'];
        $values = $this->extractPositionsValues($prevPositions);

        foreach ($values as $value) {
            if (($increased && $nextValue < $value) || (!$increased && $nextValue > $value)) {
                break;
            }

            $this->mainFuel['first_value'] = $value;
            $nextValue = $value;
        }

        return $values;
    }

    private function isIntervalValid(array $sensorValues): bool
    {
        if (empty($sensorValues)) {
            return true;
        }

        $first = $this->mainFuel['first_value'];

        sort($sensorValues);

        $count = count($sensorValues);
        $midIdx = (int)($count / 2);

        $median = $count % 2
            ? ($sensorValues[$midIdx] + $sensorValues[$midIdx + 1]) / 2
            : $sensorValues[$midIdx];

        $medianDiff = $this->getSizeDiff($median, $first);

        return $medianDiff < 0.3; // most of the values are similar to first value
    }

    private function applyIterationDiff($diff)
    {
        if (empty($diff)) {
            $this->idleDuration += self::SECONDS_GAP;
        } else {
            $this->idleDuration = 0;
            $this->mainFuel['edge_value'] = $diff['edge_value'];
            $this->mainFuel['last_value'] = $diff['last_value'];
        }
    }

    private function prolongCheck(): bool
    {
        dispatch(
            (new ConfirmFuelLevelChange(
                $this->processKey,
                $this->initTime,
                $this->eventData,
                $this->mainFuel,
                ++$this->iteration,
                $this->idleDuration
            ))->delay(self::SECONDS_GAP)
        );

        return $this->processManager->prolongLock($this->processKey);
    }

    private function getMinFillingDifference()
    {
        if ($this->device->min_fuel_fillings != 10)
            return $this->device->min_fuel_fillings;

        $max_tank = $this->deviceSensor->getMaxTankValue();

        if ($max_tank < 100)
            return $this->device->min_fuel_fillings;

        return $max_tank * 0.1;
    }

    private function getMinTheftDifference()
    {
        return $this->device->min_fuel_thefts;
    }
}