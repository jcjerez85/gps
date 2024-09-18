<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\Position;
use Tobuli\History\Stats\StatTime;

class FirstDrive extends ActionStat
{
    static public function required()
    {
        return [
            AppendMoveState::class
        ];
    }

    public function boot()
    {
        $this->registerStat('first_drive', new Position(true));
    }

    public function proccess($position)
    {
        if ($position->moving)
            $this->history->applyStat('first_drive', $position);
    }
}