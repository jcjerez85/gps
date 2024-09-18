<?php

namespace Tobuli\History\Stats;

class StatValue extends StatNumeric
{
    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        $this->value = $value;
    }
}