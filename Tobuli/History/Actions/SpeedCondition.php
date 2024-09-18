<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Stats\Distance AS DistanceStat;
use Tobuli\History\Stats\Duration AS DurationStat;
use Tobuli\History\Stats\StatCount;

class SpeedCondition extends ActionStat
{
    protected $speed;

    static public function required()
    {
        return [
            AppendDuration::class,
            AppendDistance::class,
            AppendSpeed::class,
        ];
    }

    public function boot()
    {
        $this->speed = $this->history->config('speed_break');

        $this->registerStat('speed_below_distance', new DistanceStat());
        $this->registerStat('speed_below_duration', new DurationStat());
        $this->registerStat('speed_above_distance', new DistanceStat());
        $this->registerStat('speed_above_duration', new DurationStat());
    }

    public function proccess($position)
    {
        if ($position->speed > $this->speed) {
            $this->history->applyStat("speed_above_duration", $position->duration);
            $this->history->applyStat("speed_above_distance", $position->distance);
        } else {
            $this->history->applyStat("speed_below_duration", $position->duration);
            $this->history->applyStat("speed_below_distance", $position->distance);
        }
    }
}