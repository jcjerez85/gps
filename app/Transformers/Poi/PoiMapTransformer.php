<?php

namespace App\Transformers\Poi;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Poi;

class PoiMapTransformer extends PoiTransformer {

    protected $defaultIncludes = [
        'map_icon'
    ];

    /**
     * @param Poi $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'          => (int) $entity->id,
            'group_id'    => (int) $entity->group_id ?? 0,
            'map_icon_id' => (int) $entity->map_icon_id,
            'active'      => (int) $entity->active,
            'name'        => (string) $entity->name,
            'description' => (string) $entity->description,
            'coordinates' => $entity->coordinates,
        ];
    }
}
