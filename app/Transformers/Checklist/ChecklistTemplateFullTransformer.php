<?php

namespace App\Transformers\Checklist;

use Tobuli\Entities\ChecklistTemplate;

class ChecklistTemplateFullTransformer extends ChecklistTemplateTransformer {

    /**
     * @param ChecklistTemplate $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'   => (int) $entity->id,
            'name' => (string) $entity->name,
            'type' => (int) $entity->type,
        ];
    }
}
