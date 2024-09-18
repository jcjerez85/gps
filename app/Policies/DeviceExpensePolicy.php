<?php

namespace App\Policies;

use Tobuli\Entities\User;
use Illuminate\Database\Eloquent\Model;

class DeviceExpensePolicy extends Policy
{
    protected $permisionKey = 'device_expenses';

    protected function ownership(User $user, Model $entity)
    {
        $deviceEntity = $entity->device()->first();

        if (is_null($deviceEntity))
            return false;

        return parent::ownership($user, $entity->device);
    }
}
