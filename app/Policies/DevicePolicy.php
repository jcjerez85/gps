<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\User;


class DevicePolicy extends Policy
{
    protected $permisionKey = 'devices';

    public function enable(User $user, Model $device)
    {
        return $this->update($user, $device);
    }

    public function disable(User $user, Model $device)
    {
        return $this->update($user, $device);
    }
}
