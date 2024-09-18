<?php

namespace Tobuli\History\Actions;


use Tobuli\Entities\DeviceRouteType;

class AppendDriveBusiness extends ActionAppend
{
    static public function required()
    {
        return [
            AppendDriveBusinessSensor::class,
            AppendDriveRouteType::class,
        ];
    }

    public function boot()
    {

    }

    public function proccess(&$position)
    {
        if (is_null($position->drive_route_type))
            return;

        if ($position->drive_route_type === DeviceRouteType::TYPE_BUSINESS)
            $position->drive_business = true;
        else
            $position->drive_business = null;
    }
}