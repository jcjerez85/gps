<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Stats\Duration;
use Tobuli\History\Stats\Distance;
use Tobuli\History\Stats\StatCount;
use Tobuli\History\Stats\StatValueFirst;

class Overspeed extends ActionStat
{
    static public function required()
    {
        return [
            AppendDuration::class,
            AppendDistance::class,
            AppendOverspeeding::class,
        ];
    }

    public function boot()
    {
        $this->registerStat('overspeed_duration', new Duration());
        $this->registerStat('overspeed_distance', new Distance());
        $this->registerStat('overspeed_count', new StatCount());
        $this->registerStat('overspeed_positions', new StatCount());

        $formatter = Formatter::speed();
        $this->registerStat('overspeed_limit', (new StatValueFirst())->setFormatUnit($formatter));
    }

    public function proccess($position)
    {
        if ( ! $this->isOverspeed($position))
            return;

        $this->history->applyStat("overspeed_positions", 1);
        $this->history->applyStat("overspeed_duration", $position->duration);
        $this->history->applyStat("overspeed_distance", $position->distance);

        if ($this->isStart($position)) {
            $this->history->applyStat("overspeed_limit", $position->speed_limit);
            $this->history->applyStat("overspeed_count", 1);
        }
    }

    protected function isStart($position)
    {
        return $position->overspeeding == 1;
    }

    protected function isOverspeed($position)
    {
        return isset($position->overspeeding) && $position->overspeeding;
    }
}