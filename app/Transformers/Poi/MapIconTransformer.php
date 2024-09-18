<?php

namespace App\Transformers\Poi;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\MapIcon;

class MapIconTransformer extends BaseTransformer {

    /**
     * @param MapIcon $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'     => (int) $entity->id,
            'width'  => (int) $entity->width,
            'height' => (int) $entity->height,
            'path'   => (string) $entity->path,
            'url'    => (string) $entity->url,
        ];
    }
}
