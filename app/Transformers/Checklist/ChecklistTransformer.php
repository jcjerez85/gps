<?php

namespace App\Transformers\Checklist;

use App\Transformers\BaseTransformer;
use App\Transformers\Driver\DriverFullTransformer;
use Tobuli\Entities\Checklist;

abstract class ChecklistTransformer extends BaseTransformer {

    protected $availableIncludes = [
        'rows',
        'driver',
    ];

    public function includeRows(Checklist $entity) {
        return $this->collection($entity->rows, new ChecklistRowFullTransformer(), false);
    }

    public function includeDriver(Checklist $entity) {
        $service = $entity->service;

        if (! $service) {
            return null;
        }

        if (! $service->device) {
            return null;
        }

        if (! $service->device->driver) {
            return null;
        }

        return $this->item($service->device->driver, new DriverFullTransformer(), false);
    }
}
