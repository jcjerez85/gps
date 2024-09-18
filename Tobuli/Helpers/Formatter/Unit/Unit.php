<?php

namespace Tobuli\Helpers\Formatter\Unit;

abstract class Unit
{
    protected $measure;

    protected $unit;

    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function unit()
    {
        return $this->getUnit();
    }

    public function format($value)
    {
        return $value;
    }

    public function human($value)
    {
        return "{$this->format($value)} {$this->unit()}";
    }
}