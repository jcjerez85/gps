<?php

namespace Tobuli\History\Actions;

class GroupLockStatus extends ActionGroupLockStatus
{
    protected function onChange($position, $lastStatus)
    {
        $currentStatus = $position->lock_status;

        $groupEnd = 'no_status';
        $groupStart = 'no_status';

        if (!is_null($lastStatus)) {
            $groupEnd = $currentStatus ? 'lock_off' : 'lock_on';
        }

        if (!is_null($currentStatus)) {
            $groupStart = $currentStatus ? 'lock_on' : 'lock_off';
        }

        $this->history->groupEnd($groupEnd, $position);
        $this->history->groupStart($groupStart, $position);
    }
}
