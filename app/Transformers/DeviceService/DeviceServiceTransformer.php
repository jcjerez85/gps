<?php

namespace App\Transformers\DeviceService;

use App\Transformers\BaseTransformer;
use App\Transformers\Checklist\ChecklistFullTransformer;
use Tobuli\Entities\DeviceService;

abstract class DeviceServiceTransformer extends BaseTransformer {

    protected $availableIncludes = [
        'checklists',
    ];

    public function includeChecklists(DeviceService $entity) {
        return $this->collection($entity->checklists, new ChecklistFullTransformer(), false);
    }
}
