<?php

namespace Tobuli\Helpers\Formatter\Unit;

class Capacity extends Numeric
{
    protected $precision = 2;

    public function __construct()
    {
        $this->setMeasure('lt');
    }

    public function byMeasure($unit)
    {
        switch (utf8_strtolower(utf8_substr($unit, 0, 1))) {
            case 'l':
                $this->setRatio(1);
                $this->setUnit(trans('front.l'));
                break;
            case 'g':
                $this->setRatio(0.264172053);
                $this->setUnit(trans('front.gal'));
                break;

            default:
                $this->setRatio(1);
                $this->setUnit($unit);
        }
    }
}