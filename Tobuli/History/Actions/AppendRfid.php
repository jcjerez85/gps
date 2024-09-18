<?php

namespace Tobuli\History\Actions;

class AppendRfid extends ActionAppend
{
    private $sensorRfid;

    public function boot()
    {
        $this->sensorRfid = $this->getSensor('rfid');
    }

    public function proccess(& $position)
    {
        $position->rfid = $this->sensorRfid
            ? $this->getSensorValue($this->sensorRfid, $position)
            : null;
    }
}