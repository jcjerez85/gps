<?php

namespace App\Transformers\Route;

use Tobuli\Entities\Route;

class RouteMapTransformer extends RouteTransformer
{
    protected $defaultIncludes = [];

    /**
     * @param  Route  $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (!$entity) {
            return null;
        }

        return [
            'id' => (int)$entity->id,
            'group_id' => (int)$entity->group_id ?? 0,
            'active' => (int)$entity->active,
            'name' => (string)$entity->name,
            'color' => (string)$entity->color,
            'coordinates' => $entity->coordinates,
        ];
    }
}
