<?php

namespace Tobuli\History\Stats;

class StatSum extends StatNumeric
{
    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        $this->value += $value;
    }
}