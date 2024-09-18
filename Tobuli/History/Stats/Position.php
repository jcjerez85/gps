<?php

namespace Tobuli\History\Stats;

use Formatter;
use Tobuli\Helpers\Formatter\Formattable;

class Position extends Stat
{
    protected $only_first;

    public function __construct($only_first = false)
    {
        $this->only_first = $only_first;
    }

    protected function valid($value) { return ! is_null($value); }

    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        if ( $this->only_first && ! is_null($this->value))
            return;

        $this->value = $value;
    }

    public function human()
    {
        return null;
    }

    public function __clone()
    {
        $this->value = null;
    }
}