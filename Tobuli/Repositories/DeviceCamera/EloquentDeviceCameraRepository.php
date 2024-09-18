<?php namespace Tobuli\Repositories\DeviceCamera;

use Tobuli\Repositories\EloquentRepository;
use Tobuli\Entities\DeviceCamera as Entity;

class EloquentDeviceCameraRepository extends EloquentRepository implements DeviceCameraRepositoryInterface {

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }
}
