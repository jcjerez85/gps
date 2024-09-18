<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Stats\Distance AS DistanceStat;
use Tobuli\History\Stats\Duration AS DurationStat;
use Tobuli\History\Stats\StatCount;

class DriveStop extends ActionStat
{
    protected $state;

    static public function required()
    {
        return [
            Duration::class,
            AppendDistance::class,
            AppendMoveState::class
        ];
    }

    public function boot()
    {
        $this->registerStat('drive_distance', new DistanceStat());
        $this->registerStat('drive_duration', new DurationStat());
        $this->registerStat('drive_count', new StatCount());
        $this->registerStat('stop_duration', new DurationStat());
        $this->registerStat('stop_count', new StatCount());
    }

    public function proccess($position)
    {
        switch ($position->moving)
        {
            case AppendMoveState::MOVING:
                $this->history->applyStat("drive_duration", $position->duration);
                $this->history->applyStat("drive_distance", $position->distance);

                if ($this->isChanged($position))
                    $this->history->applyStat("drive_count", 1);
                break;
            case AppendMoveState::STOPED:
                $this->history->applyStat("stop_duration", $position->duration);

                if ($this->isChanged($position))
                    $this->history->applyStat("stop_count", 1);
                break;
        }

        $this->state = $position->moving;
    }

    protected function isChanged($position)
    {
        return is_null($this->state) || $this->isStateChanged($position, 'moving');
    }
}