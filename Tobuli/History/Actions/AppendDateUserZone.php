<?php

namespace Tobuli\History\Actions;

use Formatter;

class AppendDateUserZone extends ActionAppend
{
    static public function required()
    {
        return [
            AppendDuration::class
        ];
    }

    public function boot(){}

    public function proccess(&$position)
    {
        $position->date = Formatter::time()->convert($position->time, 'Y-m-d');
    }
}
