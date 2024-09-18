<?php

namespace App\Transformers\Poi;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Poi;

abstract class PoiTransformer extends BaseTransformer {

    protected $availableIncludes = [
        'map_icon',
    ];

    public function includeMapIcon(Poi $entity) {
        if (!$entity->mapIcon)
            return null;

        return $this->item($entity->mapIcon, new MapIconTransformer(), false);
    }
}
