<?php

namespace CustomFacades\Repositories;

use Illuminate\Support\Facades\Facade;

class DeviceCameraRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Repositories\DeviceCamera\DeviceCameraRepositoryInterface';
    }
}
