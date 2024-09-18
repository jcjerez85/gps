<?php

namespace Tobuli\History\Actions;

class AppendMoveStartAt extends ActionAppend
{
    private $moveStartAt = null;

    public static function required()
    {
        return [AppendMoveState::class];
    }

    public function boot()
    {
    }

    public function proccess(&$position)
    {
        if ($position->moving === AppendMoveState::MOVING
            && ($this->getPrevPosition()->moving ?? null) === AppendMoveState::STOPED
        ) {
            $this->moveStartAt = $position->time;
        }

        $position->move_start_at = $this->moveStartAt;
    }
}