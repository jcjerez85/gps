<?php

namespace Tobuli\History\Actions;

class AppendLastStopAt extends ActionAppend
{
    private $lastStopAt = null;

    public static function required()
    {
        return [AppendMoveState::class];
    }

    public function boot()
    {
    }

    public function proccess(&$position)
    {
        if ($position->moving === AppendMoveState::STOPED) {
            $this->lastStopAt = $position->time;
        }

        $position->last_stop_at = $this->lastStopAt;
    }
}