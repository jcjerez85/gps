<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Group;

class GroupDrive extends ActionGroupMoving
{
    protected function onChange($position)
    {
        if ($position->moving) {
            $this->history->groupStart('drive', $position);
        } else {
            $this->history->groupEnd('drive', $position);
        }
    }
}