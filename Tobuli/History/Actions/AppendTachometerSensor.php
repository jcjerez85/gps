<?php


namespace Tobuli\History\Actions;


class AppendTachometerSensor extends ActionAppend
{
    protected $sensor;

    public function boot()
    {
        $this->sensor = $this->getSensor('tachometer');
    }

    public function proccess(& $position)
    {
        $position->tachometer = null;

        if ( ! $this->sensor) return;

        $position->tachometer = $this->getSensorValue($this->sensor, $position);
    }
}