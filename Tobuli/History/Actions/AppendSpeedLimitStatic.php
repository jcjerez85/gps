<?php

namespace Tobuli\History\Actions;


class AppendSpeedLimitStatic extends ActionAppend
{
    protected $speed_limit;
    
    public function boot()
    {
        $this->speed_limit = $this->history->config('speed_limit');
    }

    public function proccess(&$position)
    {
        $position->speed_limit = $this->speed_limit;
    }
}