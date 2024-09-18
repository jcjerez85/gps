<?php

namespace Tobuli\Helpers\Formatter\Unit;

class Distance extends Numeric
{
    protected $precision = 2;

    public function __construct()
    {
        $this->setMeasure('km');
    }

    public function byMeasure($unit)
    {
        switch ($unit) {
            case 'km':
                $this->setRatio(1);
                $this->setUnit(trans('front.km'));
                break;

            case 'mi':
                $this->setRatio(0.621371192);
                $this->setUnit(trans('front.mi'));
                break;

            case 'nm':
                $this->setRatio(0.539956803);
                $this->setUnit(trans('front.nm'));
                break;


            default:
                $this->setRatio(1);
                $this->setUnit($unit);
        }
    }
}