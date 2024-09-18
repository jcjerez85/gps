<?php

namespace App\Transformers\Device;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Device;

class DeviceIconTransformer extends DeviceTransformer {

    public function transform(Device $entity)
    {
        
        //dd($entity);
        return array_merge(
            $entity->icon->toArray(),
            ['color'    => $entity->getStatusColor()]
        );
    }
}