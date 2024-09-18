<?php

namespace App\Transformers\Checklist;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\ChecklistTemplate;

abstract class ChecklistTemplateTransformer extends BaseTransformer {

    protected $availableIncludes = [
        'rows',
    ];

    public function includeRows(ChecklistTemplate $entity) {
        return $this->collection($entity->rows, new ChecklistTemplateRowFullTransformer(), false);
    }

}
