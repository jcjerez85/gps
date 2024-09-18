<?php

namespace Tobuli\Helpers\Formatter\Unit;

abstract class Numeric extends Unit
{
    const OPERATION_MULTIPLE = 1;
    const OPERATION_DEVIDE = 2;

    protected $measure;
    protected $ratio = 1;
    protected $precision = 0;
    protected $operation = self::OPERATION_MULTIPLE;

    abstract public function byMeasure($unit);

    public function setMeasure($measure)
    {
        $this->measure = $measure;

        $this->byMeasure($measure);

        return $this;
    }

    public function getMeasure()
    {
        return $this->measure;
    }

    public function setRatio($ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }

    public function getRatio()
    {
        return $this->ratio;
    }

    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }

    public function getPrecision()
    {
        return $this->precision;
    }

    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    public function convert($value)
    {
        return $this->calc($this->ratio, $value);
    }

    public function reverse($value)
    {
        return $this->calc((1 / $this->ratio), $value);
    }

    public function format($value)
    {
        $converted = $this->convert($value);

        return round($converted, $this->precision);
    }

    public function human($value)
    {
        return $this->format($value) . ($this->unit() ? ' ' . $this->unit() : '');
    }

    protected function calc($ratio, $value)
    {
        $ratio = (float)$ratio;
        $value = (float)$value;

        if (!$value)
            return 0;

        switch ($this->operation) {
            case self::OPERATION_MULTIPLE:
                return $ratio * $value;

            case self::OPERATION_DEVIDE:
                return $ratio / $value;

            default:
                throw new \Exception('Unknown arithmetic operation');
        }
    }
}