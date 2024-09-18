<?php

namespace App\Transformers\Checklist;

use App\Transformers\BaseTransformer;
use Tobuli\Entities\ChecklistRow;

abstract class ChecklistRowTransformer extends BaseTransformer {

    protected $availableIncludes = [
        'images',
    ];

    public function includeImages(ChecklistRow $entity) {
        return $this->collection($entity->images, new ChecklistImageFullTransformer(), false);
    }
}
