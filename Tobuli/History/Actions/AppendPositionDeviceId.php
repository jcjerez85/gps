<?php

namespace Tobuli\History\Actions;


class AppendPositionDeviceId extends ActionAppend
{
    public function boot(){}

    public function proccess(&$position)
    {
        $position->device_id = $this->getDevice()->traccar_device_id;
    }
}