<?php

namespace Tobuli\History\Stats;

use Tobuli\Helpers\Formatter\Formattable;
use Tobuli\Helpers\Formatter\Unit\IDList;

class StatArray extends Stat
{
    protected $value = [];

    public function __construct()
    {

    }

    protected function valid($value) { return true; }

    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        $this->value[] = $value;
    }

    public function __clone()
    {
        $this->value = [];
    }
}