<?php

namespace Tobuli\History\Actions;


class AppendDrivePrivateSensor extends ActionAppend
{
    protected $sensor;

    protected $last = null;

    public function boot()
    {
        if ( ! settings('plugins.business_private_drive.status') )
            return;

        $this->sensor = $this->getSensor('drive_private');
    }

    public function proccess(&$position)
    {
        if ( ! $this->sensor)
            return;

        $position->drive_private = $this->last;

        $value = $this->getSensorValue($this->sensor, $position, null);

        if (is_null($value))
            return;

        $position->drive_private = $this->last = $value;
    }
}