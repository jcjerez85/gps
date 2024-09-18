<?php

namespace Tobuli\Helpers\Formatter\Unit;

class Weight extends Numeric
{
    public function __construct()
    {
        $this->setMeasure('kg');
    }

    public function byMeasure($unit)
    {
        switch ($unit) {
            case 'kg':
                $this->setRatio(1);
                $this->setUnit('kg');
                break;

            default:
                $this->setRatio(1);
                $this->setUnit($unit);
        }
    }
}