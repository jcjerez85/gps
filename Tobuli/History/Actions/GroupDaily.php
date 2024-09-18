<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Group;

class GroupDaily extends ActionGroup
{
    private $current;

    /**
     * @var Group
     */
    private $group;

    static public function required()
    {
        return [
            AppendDateUserZone::class,
        ];
    }

    public function boot()
    {
    }

    public function proccess($position)
    {
        if ($this->current == $position->date)
            return;

        if ( ! is_null($this->group))
            $this->history->groupEnd($this->group->getKey(), $position);

        $this->group = new Group($position->date);
        $this->group->daily = true;

        $this->history->groupStart($this->group, $position);

        $this->current = $position->date;
    }
}