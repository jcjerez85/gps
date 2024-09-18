<?php

namespace App\Transformers\Checklist;

use Tobuli\Entities\Checklist;

class ChecklistFullTransformer extends ChecklistTransformer {

    /**
     * @param Checklist $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'             => (int) $entity->id,
            'template_id'    => (int) $entity->template_id,
            'service_id'     => (int) $entity->service_id,
            'name'           => (string) $entity->name,
            'type'           => (int) $entity->type,
            'signature'      => $entity->signature,
            'completed_at'   => $entity->completed_at,
            'notes'          => $entity->notes,
        ];
    }
}
