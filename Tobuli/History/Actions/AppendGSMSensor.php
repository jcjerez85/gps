<?php


namespace Tobuli\History\Actions;


class AppendGSMSensor extends ActionAppend
{

    protected $sensor;

    public function boot()
    {
        $this->sensor = $this->getSensor('gsm');
    }

    public function proccess(& $position)
    {
        $position->gsm = null;

        if ( ! $this->sensor) return;

        $position->gsm = $this->getSensorValue($this->sensor, $position);
    }
}