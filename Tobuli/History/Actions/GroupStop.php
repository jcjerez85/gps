<?php

namespace Tobuli\History\Actions;


class GroupStop extends ActionGroupMoving
{
    protected function onChange($position)
    {
        if ($position->moving)
            $this->history->groupEnd('stop', $position);
        else
            $this->history->groupStart('stop', $position);
    }
}