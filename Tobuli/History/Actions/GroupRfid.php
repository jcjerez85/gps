<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Group;

class GroupRfid extends ActionQuit
{
    const KEY = 'rfid';

    private $prev = null;

    public static function required()
    {
        return [AppendRfid::class];
    }

    public function boot()
    {
    }

    public function proccess(&$position)
    {
        if (!$position->rfid || $this->prev === $position->rfid) {
            return;
        }

        $this->history->groupEnd(self::KEY, $position);

        if ($this->isQuitable($position)) {
            $position->quit = true;
            return;
        }

        $this->history->groupStart(new Group(self::KEY), $position);

        $this->prev = $position->rfid;
    }

    protected function isQuitable($position): bool
    {
        return false;
    }
}