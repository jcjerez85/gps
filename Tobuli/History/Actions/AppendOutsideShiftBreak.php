<?php

namespace Tobuli\History\Actions;


class AppendOutsideShiftBreak extends ActionBreak
{
    const RADIO = 5000;

    public static function required()
    {
        return [AppendShiftCheck::class];
    }

    public function boot()
    {
    }

    protected function isBreakable($position)
    {
        return !$position->inShift;
    }
}