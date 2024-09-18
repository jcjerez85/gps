<?php

namespace App\Transformers\ApiV1;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\DeviceGroup;

class DeviceGroupTransformer extends BaseTransformer
{
    public function transform(?DeviceGroup $entity): ?array
    {
        if (!$entity) {
            return null;
        }

        return [
            'id' => (int)$entity->id,
            'user_id' => (int)$entity->user_id,
            'title' => (string)$entity->title,
            'open' => (bool)$entity->open,
        ];
    }
}
