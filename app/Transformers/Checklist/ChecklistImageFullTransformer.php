<?php

namespace App\Transformers\Checklist;

use Tobuli\Entities\ChecklistImage;

class ChecklistImageFullTransformer extends ChecklistImageTransformer {

    /**
     * @param ChecklistImage $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'                   => (int) $entity->id,
            'checklist_id'         => (int) $entity->checklist_id,
            'row_id'               => (int) $entity->row_id,
            'checklist_history_id' => $entity->checklist_history_id,
            'url'                  => $entity->path ? url($entity->path) : null,
        ];
    }
}
