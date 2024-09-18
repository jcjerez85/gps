<?php

namespace App\Transformers\Checklist;

use Tobuli\Entities\ChecklistTemplateRow;

class ChecklistTemplateRowFullTransformer extends ChecklistTemplateRowTransformer {

    /**
     * @param ChecklistTemplateRow $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'          => (int) $entity->id,
            'template_id' => (int) $entity->template_id,
            'activity'    => (string) $entity->activity,
        ];
    }
}
