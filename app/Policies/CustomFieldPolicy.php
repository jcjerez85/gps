<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\User;

class CustomFieldPolicy extends Policy
{
    protected $permisionKey = 'custom_field';

    protected function ownership(User $user, Model $entity)
    {
        return $user->isAdmin();
    }
}
