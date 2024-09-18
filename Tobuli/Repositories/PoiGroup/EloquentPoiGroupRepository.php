<?php namespace Tobuli\Repositories\PoiGroup;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Entities\PoiGroup as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentPoiGroupRepository extends EloquentRepository implements PoiGroupRepositoryInterface {

    protected $user;

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }
}