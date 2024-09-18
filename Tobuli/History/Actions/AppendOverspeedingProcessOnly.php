<?php

namespace Tobuli\History\Actions;


class AppendOverspeedingProcessOnly extends ActionAppend
{
    static public function required()
    {
        return [
            AppendOverspeeding::class,
        ];
    }

    public function boot() {}

    public function proccess(&$position)
    {
        $position->only_overspeeding = empty($position->overspeeding) ? false : true;
    }
}