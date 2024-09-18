<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\Position;

class LastEngine extends ActionStat
{
    static public function required()
    {
        return [
            AppendEngineStatus::class
        ];
    }

    public function boot()
    {
        $this->registerStat('last_engine', new Position());
    }

    public function proccess($position)
    {
        if ($position->engine)
            $this->history->applyStat("last_engine", $position);
    }
}