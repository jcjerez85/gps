<?php

namespace Tobuli\History\Stats;

class StatDiff extends StatNumeric
{
    protected $first;

    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        if (is_null($this->first))
            $this->first = $value;

        $this->value = $value - $this->first;
    }

    public function __clone()
    {
        $this->value = null;
        $this->first = null;
    }
}