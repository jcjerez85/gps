<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Group;

class GroupQuarterHour extends ActionGroup
{
    const KEY = 'quarter';

    private $current;

    /**
     * @var Group
     */
    private $group;

    static public function required()
    {
        return [
            AppendQuarterHour::class,
            //AppendDurationQuarterHour::class,
        ];
    }

    public function boot()
    {
    }

    public function proccess($position)
    {
        if ($this->current == $position->quarter)
            return;

        if ( ! is_null($this->group))
            $this->history->groupEnd($this->group->getKey(), $position);

        $this->group = new Group(self::KEY);

        $this->history->groupStart($this->group, $position);

        $this->current = $position->quarter;
    }
}