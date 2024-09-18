<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Group;

class GroupSingle extends ActionGroup
{
    protected $state;

    static public function required()
    {
        return [];
    }

    public function boot() {}

    public function proccess($position)
    {
        if ( ! is_null($this->state))
            return;

        $this->history->groupStart('single', $position);

        $this->state = true;
    }
}