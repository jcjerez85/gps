<?php

namespace Tobuli\History\Stats;

class StatAvg extends StatNumeric
{
    protected $count;

    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        $this->value = ($this->count * $this->value + $value) / ($this->count + 1);

        $this->count++;
    }

    public function __clone()
    {
        $this->value = null;
        $this->count = 0;
    }
}