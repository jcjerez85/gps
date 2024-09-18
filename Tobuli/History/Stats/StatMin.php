<?php

namespace Tobuli\History\Stats;

class StatMin extends StatNumeric
{
    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        if (is_null($this->value))
            $this->value = $value;

        if ($this->value > $value)
            $this->value = $value;
    }
}