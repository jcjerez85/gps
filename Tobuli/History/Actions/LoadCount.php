<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatCount;

class LoadCount extends ActionStat
{
    use LoadTrait;

    private $countName;

    static public function required()
    {
        return [AppendLoadChange::class];
    }

    public function boot()
    {
        foreach (static::$summarize ? [null] : static::$loadStates as $state) {
            $this->registerStat($this->getLoadStateName($state) . '_count', new StatCount());
        }
    }

    public function proccess($position)
    {
        if (static::isPositionLoadValid($position)) {
            $this->history->applyStat($this->getPositionLoadStateName($position) . '_count', 1);
        }
    }
}