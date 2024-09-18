<?php

namespace App\Policies\Property;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\User;

class UserPropertiesPolicy extends PropertyPolicy
{
    protected $entity = 'user';

    protected $viewable = ['login_token', 'client_id'];

    protected $editable = [
        'billing_plan_id',
        'login_token',
        'devices_limit',
        'subscription_expiration',
        'expiration_date',
        'group_id',
        'manager_id',
        'client_id',
        'forwards',
        'login_periods',
        'password',
    ];

    protected $selfNotEditable = [
        'active',
        'billing_plan_id',
        'devices_limit',
        'subscription_expiration',
        'expiration_date',
        'group_id',
        'manager_id',
        'login_periods',
    ];

    protected function forwardsEditPolicy(User $user, Model $model)
    {
        return config('addon.forwards') && ($user->isAdmin() || $user->isSupervisor());
    }

    protected function passwordEditPolicy(User $user, Model $model)
    {
        return $user->isMainLogin() || $user->id !== $model->id;
    }

    protected function loginPeriodsEditPolicy()
    {
        return settings('login_periods.enabled');
    }

    protected function _edit(User $user, Model $model, $property)
    {
        if ($this->canSelfEdit($user, $model, $property) === false)
            return false;

        return true;
    }

    private function canSelfEdit(User $user, Model $model, $property): bool
    {
        return !($model->id === $user->id && in_array($property, $this->selfNotEditable));
    }
}