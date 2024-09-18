<?php

namespace App\Transformers\ApiV1;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Poi;

class PoiTransformer extends BaseTransformer {

    protected $availableIncludes = [
        'map_icon',
    ];

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
            'user_id'     => (int) $entity->user_id,
            'map_icon_id' => (int) $entity->map_icon_id,
            'active'      => (int) $entity->active,
            'name'        => (string) $entity->name,
            'description' => (string) $entity->description,
            'coordinates' => (string) json_encode($entity->coordinates),
            'created_at'  => (string) $entity->created_at,
            'updated_at'  => (string) $entity->updated_at,
        ];
    }

    public function includeMapIcon(Poi $entity) {
        return $this->item($entity->mapIcon, new MapIconTransformer(), false);
    }
}
