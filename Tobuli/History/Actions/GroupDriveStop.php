<?php

namespace Tobuli\History\Actions;


class GroupDriveStop extends ActionGroupMoving
{
    protected function onChange($position)
    {
        $this->history->groupEnd($position->moving ? 'stop' : 'drive', $position);
        $this->history->groupStart($position->moving ? 'drive' : 'stop', $position);
    }
}