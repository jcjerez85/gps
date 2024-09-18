<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Group;

class GroupShift extends ActionGroup
{
    public static function required()
    {
        return [AppendShiftCheck::class];
    }

    private ?Group $group = null;

    public function boot()
    {
    }

    public function proccess($position)
    {
        $inShift = $position->inShift;
        $open = $this->group && $this->group->isOpen();

        if ($inShift && !$open) {
            $this->group = new Group($position->time);
            $this->group->setStartPosition($position);

            $this->history->groupStart($this->group, $position);

            return;
        }

        if (!$inShift && $open) {
            $this->history->groupEnd($this->group->getKey(), $position);
        }
    }
}