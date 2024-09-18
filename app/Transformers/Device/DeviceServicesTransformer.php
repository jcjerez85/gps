<?php

namespace App\Transformers\Device;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Device;

class DeviceServicesTransformer extends DeviceTransformer {

    public function transform(Device $entity) {
        $result = [];

        foreach ($entity->services as $service)
        {
            $service->setSensors($entity->sensors);

            $result[] = [
                'id'       => (int)$service->id,
                'name'     => $service->name,
                'value'    => $service->expiration(),
                'expiring' => (bool)$service->isExpiring()
            ];
        }

        return $result;
    }
}