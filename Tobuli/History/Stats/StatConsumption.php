<?php

namespace Tobuli\History\Stats;

class StatConsumption extends StatNumeric
{
    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        $this->value += $value;
    }

    public function get()
    {
        return $this->value < 0 ? 0 : $this->value;
    }
}