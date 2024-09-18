<?php

namespace Tobuli\History\Actions;


class AppendUnderspeed extends ActionAppend
{
    static public function required()
    {
        return [
            AppendSpeedLimitStatic::class,
            AppendSpeed::class,
        ];
    }

    public function boot()
    {
    }

    public function proccess(&$position)
    {
        $position->underspeeding = 0;

        if ( ! $this->isUnderspeed($position))
            return;

        $position->underspeeding++;

        if ($previous = $this->getPrevPosition())
            $position->underspeeding += $previous->underspeeding;
    }

    protected function isUnderspeed($position)
    {
        return ! is_null($position->speed_limit) && $position->speed_limit > $position->speed;
    }
}