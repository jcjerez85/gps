<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\User;

class UserPolicy extends Policy
{
    protected $permisionKey = 'users';

    protected function ownership(User $user, Model $entity)
    {
        if ($user->isSupervisor()) {
            return true;
        }

        if ($user->isManager() && $user->id == $entity->manager_id)
            return true;

        if ($user->id == $entity->id)
            return true;

        if ($entity && !$entity->exists)
            return true;

        return false;
    }

    protected function hasPermission(User $user, Model $entity, $mode)
    {
        if ($user->id == $entity->id && in_array($mode, ['view', 'edit']))
            return true;

        return parent::hasPermission($user, $entity, $mode);
    }

    public function destroy(User $user, Model $entity = null)
    {
        if ($user->id == $entity->id)
            return false;

        if ($user->isAdmin())
            return true;

        return $this->clean($user, $entity);
    }
}
