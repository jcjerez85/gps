<?php

namespace Tobuli\History\Stats;

use Formatter;
use Tobuli\Helpers\Formatter\Formattable;

class Distance extends StatSum implements Skippable
{
    use Formattable;

    protected $skipped;

    public function __construct()
    {
        parent::__construct();

        $this->setFormatUnit(Formatter::distance());
    }

    public function apply($value)
    {
        if ( ! $this->skipped && $this->setSkipped()) {
            return;
        }

        parent::apply($value);
    }

    public function setSkipped()
    {
        return $this->skipped = true;
    }

    public function __clone()
    {
        parent::__clone();

        $this->skipped = null;
    }
}