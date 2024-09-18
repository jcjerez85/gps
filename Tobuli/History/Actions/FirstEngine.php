<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\Position;

class FirstEngine extends ActionStat
{
    static public function required()
    {
        return [
            AppendEngineStatus::class
        ];
    }

    public function boot()
    {
        $this->registerStat('first_engine', new Position(true));
    }

    public function proccess($position)
    {
        if ($position->engine)
            $this->history->applyStat('first_engine', $position);
    }
}