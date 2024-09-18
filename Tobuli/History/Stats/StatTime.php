<?php

namespace Tobuli\History\Stats;

use Formatter;
use Tobuli\Helpers\Formatter\Formattable;

class StatTime extends Stat
{
    protected $only_first;

    use Formattable;

    public function __construct($only_first = false)
    {
        $this->only_first = $only_first;

        $this->setFormatUnit(Formatter::time());
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

    public function __clone()
    {
        $this->value = null;
    }
}