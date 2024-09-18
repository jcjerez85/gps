<?php

namespace Tobuli\History\Actions;


use Tobuli\Entities\DeviceRouteType;

class AppendDrivePrivate extends ActionAppend
{
    static public function required()
    {
        return [
            AppendDrivePrivateSensor::class,
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

        if ($position->drive_route_type === DeviceRouteType::TYPE_PRIVATE)
            $position->drive_private = true;
        else
            $position->drive_private = null;
    }
}