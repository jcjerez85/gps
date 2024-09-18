<?php

namespace Tobuli\History\Stats;

use Tobuli\Helpers\Formatter\Formattable;
use Tobuli\Helpers\Formatter\Unit\IDList;

class StatList extends Stat
{
    protected $value = [];

    use Formattable;

    public function __construct()
    {
        $this->setFormatUnit(new IDList());
    }

    protected function valid($value) { return true; }

    public function apply($value)
    {
        if ( ! $this->valid($value))
            return;

        if (is_array($value)) {
            foreach ($value as $val)
                $this->apply($val);
        } elseif ( ! in_array($value, $this->value)) {
            $this->value[] = $value;
        }
    }

    public function __clone()
    {
        $this->value = [];
    }
}