<?php

namespace App\Transformers\Sharing;

use App\Transformers\BaseTransformer;
use App\Transformers\Device\DeviceListTransformer;
use League\Fractal\Resource\Collection;
use Tobuli\Entities\Sharing;

abstract class AbstractSharingTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        'devices',
    ];

    public function includeDevices(Sharing $entity): Collection
    {
        return $this->collection($entity->devices, new DeviceListTransformer(), false);
    }
}
