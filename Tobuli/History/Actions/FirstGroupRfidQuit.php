<?php

namespace Tobuli\History\Actions;

class FirstGroupRfidQuit extends GroupRfid
{
    protected function isQuitable($position): bool
    {
        foreach ($this->history->groups()->all() as $group) {
            if ($group->getKey() === self::KEY && $group->isClose()) {
                return true;
            }
        }

        return false;
    }
}