<?php
namespace App\Policies\Action;

use \Tobuli\Entities\User;

class ConfigureDevicePolicy extends ActionPolicy
{
    /* Check if user can configure device
    *
    * @param  User $user User model
    * @return Boolean
    */
    public function able(User $user)
    {
        if ( ! $user->perm('device_configuration', 'view'))
            return false;

        if (settings('sms_gateway.use_as_system_gateway'))
            return true;

        if ($user->sms_gateway)
            return true;

        return false;
    }
}
