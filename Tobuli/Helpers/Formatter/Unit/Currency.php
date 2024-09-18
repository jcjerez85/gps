<?php

namespace Tobuli\Helpers\Formatter\Unit;

class Currency extends Numeric
{
    protected $precision = 2;

    public function __construct()
    {
        $this->setMeasure(settings('currency.symbol'));
    }

    public function byMeasure($unit)
    {
        $this->setRatio(1);
        $this->setUnit($unit);
    }
}