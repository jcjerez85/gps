<?php

namespace Tobuli\Helpers\Formatter\Unit;

class Speed extends Numeric
{
    public function __construct()
    {
        $this->setMeasure('km');
    }

    public function byMeasure($unit)
    {
        switch ($unit) {
            case 'km':
                $this->setRatio(1);
                $this->setUnit(trans('front.dis_h_km'));
                break;

            case 'mi':
                $this->setRatio(0.621371192);
                $this->setUnit(trans('front.dis_h_mi'));
                break;

            case 'nm':
            case 'kn':
                $this->setRatio(0.54);
                $this->setUnit(trans('front.kn'));
                break;

            default:
                $this->setRatio(1);
                $this->setUnit($unit);
        }
    }
}