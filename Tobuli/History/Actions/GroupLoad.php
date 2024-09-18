<?php

namespace Tobuli\History\Actions;

use Formatter;
use Tobuli\History\Group;
use Tobuli\History\Stats\StatValue;

class GroupLoad extends ActionGroup
{
    use LoadTrait;

    private $groupName;

    static public function required()
    {
        return [AppendLoadChange::class];
    }

    public function boot()
    {
    }

    public function proccess($position)
    {

        if (!static::isPositionLoadValid($position)) {

            return;
        }

        $groupName = $this->getPositionLoadStateName($position);

        $group = new Group($groupName);

        $valuesMap = [
            'previous_load',
            'current_load',
            'difference',
        ];

        $weightUnit = Formatter::weight();

        foreach ($valuesMap as $value) {
            $stat = (new StatValue())->setFormatUnit($weightUnit);
            $stat->apply($position->loadChange[$value]);

            $group->stats()->set($value, $stat);
        }

        $this->history->groupStart($group, $position);
        $this->history->groupEnd($groupName, $position);
    }
}