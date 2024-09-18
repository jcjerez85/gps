<?php

namespace App\Jobs;

use Carbon\Carbon;

class ConfirmFuelLevelFuture extends AbstractConfirmFuelLevel
{
    public function __construct(
        $processKey,
        $time,
        array $eventData,
        array $mainFuel,
        int $iteration
    ) {
        parent::__construct($processKey, $time, $eventData, $mainFuel, $iteration);
    }

    public function handle()
    {
        return $this->checkAfterHistory() && $this->sendEvent();
    }

    private function checkAfterHistory(): bool
    {
        $timeFrom = Carbon::parse($this->initTime)->addSeconds(self::SECONDS_GAP * ($this->iteration - 1));
        $timeTo = (clone $timeFrom)->addSeconds(self::FINALIZATION_GAP);

        $positions = $this->device->positions()
            ->where('time', '>', $timeFrom)
            ->where('time', '<=', $timeTo)
            ->orderliness('ASC')
            ->get();

        $values = $this->extractPositionsValues($positions);

        $count = count($values);
        $mainEdge = $this->mainFuel['edge_value'];

        $avg = $count ? array_sum($values) / $count : $mainEdge;

        return $this->getSizeDiff($avg, $mainEdge, 0) <= 0.3;
    }
}