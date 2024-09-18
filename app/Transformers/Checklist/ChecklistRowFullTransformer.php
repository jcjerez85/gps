<?php

namespace App\Transformers\Checklist;

use Tobuli\Entities\ChecklistRow;

class ChecklistRowFullTransformer extends ChecklistRowTransformer {

    /**
     * @param ChecklistRow $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'              => (int) $entity->id,
            'checklist_id'    => (int) $entity->checklist_id,
            'template_row_id' => (int) $entity->template_row_id,
            'activity'        => (string) $entity->activity,
            'completed'       => (bool) $entity->completed,
            'completed_at'    => $entity->completed_at,
            'outcome'         => $entity->outcome,
        ];
    }
}
