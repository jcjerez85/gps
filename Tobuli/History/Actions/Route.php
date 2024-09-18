<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Group;

class Route extends ActionStat
{
    protected $color;
    protected $point;

    protected $routes = [];
    protected $reference;

    static public function required()
    {
        return [
            AppendPosition::class,
            AppendRouteColor::class,
        ];
    }

    public function boot() {}

    public function proccess($position)
    {
        $this->history->applyRoute($position);

        return;
    }

    protected function setInGroup(Group & $group, $position)
    {
        $group->route()->apply($position);
    }
}