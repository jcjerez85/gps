<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\User;

class EmailTemplatePolicy extends Policy
{
    protected $permisionKey = null;

    protected function ownership(User $user, Model $entity)
    {
        if ($user->isManager() && $user->id == $entity->user_id)
            return true;

        return false;
    }

    public function destroy(User $user, Model $entity = null)
    {
        if (is_null($entity->user_id))
            return false;

        return $this->clean($user, $entity);
    }
}
