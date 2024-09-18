<?php

namespace Tobuli\History\Stats;

use Formatter;
use Tobuli\Helpers\Formatter\Formattable;

class Duration extends StatSum implements Skippable
{
    use Formattable;

    protected $skipped = false;

    public function __construct()
    {
        parent::__construct();

        $this->setFormatUnit(Formatter::duration());
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