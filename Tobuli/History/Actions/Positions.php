<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatArray;

class Positions extends ActionStat
{
    static public function required()
    {
        return [
            AppendPositionDeviceId::class,
            AppendPositionIndex::class,
            AppendPosition::class,
            AppendDistance::class,
            AppendRouteColor::class,
        ];
    }

    public function boot()
    {
        $this->registerStat('positions', (new StatArray()));
    }

    public function proccess($position)
    {
        $this->history->applyStat('positions', $position);

        return;
    }
}