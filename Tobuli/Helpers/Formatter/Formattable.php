<?php


namespace Tobuli\Helpers\Formatter;


use Tobuli\Helpers\Formatter\Unit\Unit;

trait Formattable
{
    protected $caller;
    protected $formattUnit;

    public function caller()
    {
        if (is_null($this->caller))
            $this->caller = new Caller($this->formattUnit, $this);

        return $this->caller;
    }

    public function setFormatUnit(Unit $unit)
    {
        $this->formattUnit = $unit;

        return $this;
    }

    public function getFormatUnit()
    {
        return $this->formattUnit;
    }

    public function setMeasure($unit)
    {
        $this->formattUnit->setMeasure($unit);

        if ( ! is_null($this->caller)) {
            $this->caller = null;
            $this->caller();
        }

        return $this;
    }

    public function convert()
    {
        return $this->caller()->method( 'convert')->get();
    }

    public function format()
    {
        return $this->caller()->method( 'format')->get();
    }

    public function human()
    {
        return $this->caller()->method('human')->get();
    }
}