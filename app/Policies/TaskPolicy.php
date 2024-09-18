<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\User;

class TaskPolicy extends Policy
{
    protected $permisionKey = 'tasks';

    protected function ownership(User $user, Model $entity)
    {
        //user created task
        if (parent::ownership($user, $entity))
            return true;

        return $user->can('view', $entity->device);
    }
}
