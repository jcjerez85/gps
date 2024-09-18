<?php

namespace Tobuli\Helpers\Formatter\Unit;

class Altitude extends Numeric
{
    public function __construct()
    {
        $this->setMeasure('mt');
    }

    public function byMeasure($unit)
    {
        switch ($unit) {
            case 'mt':
                $this->setRatio(1);
                $this->setUnit(trans('front.mt'));
                break;

            case 'ft':
                $this->setRatio(3.2808399);
                $this->setUnit(trans('front.ft'));
                break;

            default:
                $this->setRatio(1);
                $this->setUnit($unit);
        }
    }
}