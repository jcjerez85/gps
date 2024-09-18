<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Group;

class GroupDailySplit extends ActionGroup
{
    protected $current;

    static public function required()
    {
        return [
            AppendPosition::class,
            AppendDayChange::class,
            AppendDurationDailySplit::class,
        ];
    }

    public function boot() {}

    public function proccess($position)
    {
        if (!$position->day_change)
            return;

        $groups = $this->history->getGroups()->actives();

        foreach ($groups as $group)
        {
            if (!empty($group->daily))
                continue;

            $this->history->groupEnd($group->getKey(), $position);

            $regroup = new Group($group->getKey());
            $regroup->setMetaContainer( $group->getMetaContainer() );

            $this->history->groupStart($regroup, $position);
        }
    }
}