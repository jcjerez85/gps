<?php namespace Tobuli\Repositories\TraccarDevice;

use Tobuli\Entities\TraccarDevice as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentTraccarDeviceRepository extends EloquentRepository implements TraccarDeviceRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}