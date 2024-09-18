<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatCount;

class AppendCount extends ActionAppend
{
    public function boot()
    {
        $this->registerStat('position_count', new StatCount());
    }

    public function proccess(&$position)
    {
        $this->history->applyStat('position_count', 1);
    }
}