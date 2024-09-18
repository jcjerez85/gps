<?php


namespace Tobuli\Helpers\Formatter\Unit;


class Vain extends Numeric
{
    protected $precision = 2;

    public function byMeasure($unit) {
        $this->setUnit($unit);
    }
}