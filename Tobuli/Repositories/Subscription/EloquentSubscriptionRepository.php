<?php

namespace Tobuli\Repositories\Subscription;

use Tobuli\Entities\Subscription as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentSubscriptionRepository extends EloquentRepository implements SubscriptionRepositoryInterface
{
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function expired()
    {
        $this->entity->where('expiration_date', '<', date('Y-m-d'))->get();
    }
}