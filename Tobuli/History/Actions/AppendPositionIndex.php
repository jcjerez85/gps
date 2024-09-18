<?php

namespace Tobuli\History\Actions;

use Tobuli\History\Stats\StatCount;

class AppendPositionIndex extends ActionAppend
{
    protected $index = 0;

    public function boot() {}

    public function proccess(&$position)
    {
        $this->index++;

        $position->index = $this->index;
    }
}