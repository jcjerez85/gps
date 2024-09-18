<?php

namespace Tobuli\History\Actions;


class AppendDriveBusinessSensor extends ActionAppend
{
    protected $sensor;

    protected $last = null;

    public function boot()
    {
        if ( ! settings('plugins.business_private_drive.status') )
            return;

        $this->sensor = $this->getSensor('drive_business');
    }

    public function proccess(&$position)
    {
        if ( ! $this->sensor)
            return;

        $position->drive_business = $this->last;

        $value = $this->getSensorValue($this->sensor, $position, null);

        if (is_null($value))
            return;

        $position->drive_business = $this->last = $value;
    }
}