<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\Company;
use Tobuli\Entities\User;

class CompanyPolicy extends Policy
{
    public function view(User $user, Model $entity = null)
    {
        return true;
    }

    /**
     * @param  User  $user
     * @param  Company  $entity
     * @return bool
     * @throws \Exception
     */
    protected function ownership(User $user, Model $entity)
    {
        if ($user->isSupervisor()) {
            return true;
        }

        if (!$entity->owner) {
            return false;
        }

        return $entity->owner->id === $user->id || $entity->owner->manager_id === $user->id;
    }
}
