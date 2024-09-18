<?php

namespace App\Transformers\ApiV1;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\Alert;
use Formatter;

class DeviceAlertListTransformer extends BaseTransformer {

    /**
     * @param Alert $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'        => (int) $entity->id,
            'name'      => (string) $entity->name,
            'active'    => (bool) $entity->isActive(),
            'date_from' => empty($entity->pivot->active_from) ? null : Formatter::time()->human($entity->pivot->active_from),
            'date_to'   => empty($entity->pivot->active_to) ? null : Formatter::time()->human($entity->pivot->active_to),
        ];
    }
}
