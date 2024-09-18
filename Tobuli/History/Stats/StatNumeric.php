<?php

namespace Tobuli\History\Stats;

use Tobuli\Helpers\Formatter\Formattable;
use Tobuli\Helpers\Formatter\Unit\Vain;

abstract class StatNumeric Extends Stat
{
    use Formattable;

    protected function valid($value) { return is_numeric($value); }

    public function __construct()
    {
        $this->setFormatUnit(new Vain());
    }

    public function __clone()
    {
        $this->value = null;
    }
/*
    public function __toString()
    {
        return $this->get();
    }
*/
}