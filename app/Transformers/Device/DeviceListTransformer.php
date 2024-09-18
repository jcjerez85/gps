<?php

namespace App\Transformers\Device;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Device;

class DeviceListTransformer extends DeviceTransformer  {

    public function transform(Device $entity)
    {
        return [
            'id'    => (int)$entity->id,
            'name'  => $entity->name,
        ];
    }
}