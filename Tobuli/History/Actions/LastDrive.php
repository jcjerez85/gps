<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\Position;
use Tobuli\History\Stats\StatTime;

class LastDrive extends ActionStat
{
    static public function required()
    {
        return [
            AppendMoveState::class
        ];
    }

    public function boot()
    {
        $this->registerStat('last_drive', new Position());
    }

    public function proccess($position)
    {
        if ($position->moving)
            $this->history->applyStat("last_drive", $position);
    }
}