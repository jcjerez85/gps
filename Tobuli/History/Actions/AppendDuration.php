<?php

namespace Tobuli\History\Actions;


class AppendDuration extends ActionAppend
{
    static public function required()
    {
        return [
            AppendCount::class
        ];
    }

    public function boot(){}

    public function proccess(&$position)
    {
        $position->timestamp = strtotime($position->time);

        $position->duration = 0;

        if ($previous = $this->getPrevPosition())
            $position->duration = $position->timestamp - $previous->timestamp;
    }
}