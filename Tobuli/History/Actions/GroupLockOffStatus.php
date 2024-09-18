<?php

namespace Tobuli\History\Actions;

class GroupLockOffStatus extends ActionGroupLockStatus
{
    protected function onChange($position, $lastStatus)
    {
        if (! $position->lock_status) {
            $this->history->groupStart('lock_off', $position);
        } else {
            $this->history->groupEnd('lock_off', $position);
        }
    }
}
