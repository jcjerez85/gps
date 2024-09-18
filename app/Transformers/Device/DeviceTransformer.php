<?php

namespace App\Transformers\Device;

use App\Transformers\BaseTransformer;
use App\Transformers\Driver\DriverFullTransformer;
use Tobuli\Entities\Device;

abstract class DeviceTransformer extends BaseTransformer {


    protected $availableIncludes = [
        'position',
        'icon',
        'sensors',
        'services',
        'driver',
        'users'
    ];

    public function includePosition(Device $device) {
        return $this->item($device, new DevicePositionTransformer(), false);
    }

    public function includeIcon(Device $device) {
        return $this->item($device, new DeviceIconTransformer(), false);
    }

    public function includeSensors(Device $device) {
        return $this->item($device, new DeviceSensorsTransformer(), false);
    }

    public function includeServices(Device $device) {
        return $this->item($device, new DeviceServicesTransformer(), false);
    }

    public function includeDriver(Device $device) {
        if ( ! $device->driver)
            return null;

        return $this->item($device->driver, new DriverFullTransformer(), false);
    }

    public function includeUsers(Device $device) {
        return $this->item($device, new DeviceUsersTransformer(), false);
    }
}