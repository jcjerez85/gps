<?php

namespace Tobuli\Helpers\Formatter\Unit;

class FuelAvg extends Numeric
{
    const PER_DISTANCE = 'distance';
    const PER_CAPACITY = 'capacity';

    protected $precision = 2;
    protected $per = self::PER_DISTANCE;

    public function __construct()
    {
        $this->setMeasure('lt/km');
    }

    public function setPer($per)
    {
        $this->per = $per;
    }

    public function getPer()
    {
        return $this->per;
    }

    public function byMeasure($unit)
    {
        list($capacity, $distance) = $this->parseMeasures($unit);

        switch ($this->getPer()) {
            case self::PER_CAPACITY:
                $_unit = "$distance/$capacity";
                break;
            case self::PER_DISTANCE:
            default:
                $_unit = "$capacity/$distance";
                break;
        }

        switch ($_unit) {
            case 'km/l':
            case 'km/lt':
                $this->setRatio(1);
                $this->setOperation(self::OPERATION_DEVIDE);
                $this->setUnit(trans('front.km') . '/' . trans('front.l'));
                break;

            case 'lt/km':
            case 'l/km':
                $this->setRatio(100);
                $this->setOperation(self::OPERATION_MULTIPLE);
                $this->setUnit(trans('front.l') . '/100' . trans('front.km'));
                break;

            //MPG
            case 'mi/g':
            case 'mi/gl':
                $this->setRatio(2.35224);
                $this->setOperation(self::OPERATION_DEVIDE);
                $this->setUnit(trans('front.mpg'));
                break;

            case 'g/mi':
            case 'gl/mi':
                $this->setRatio(0.4251437135);
                $this->setOperation(self::OPERATION_MULTIPLE);
                $this->setUnit(trans('front.gal') . '/100' . trans('front.mi'));
                break;

            default:
                $this->setRatio(1);
                $this->setOperation(self::OPERATION_MULTIPLE);
                $this->setUnit($unit);
        }
    }

    private function parseMeasures($unit) {
        list($capacityUnit, $distanceUnit) = explode('/', $unit);

        return [
            strtolower(trim($capacityUnit)),
            strtolower(trim($distanceUnit)),
        ];
    }
}