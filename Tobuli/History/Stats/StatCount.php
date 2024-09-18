<?php

namespace Tobuli\History\Stats;

class StatCount extends StatNumeric
{
    protected $value = 0;

    public function apply($value = null)
    {
        if (is_null($value))
            $value = 1;

        if ( ! $this->valid($value))
            return;

        $this->value += $value;
    }

    public function __clone()
    {
        $this->value = 0;
    }
}