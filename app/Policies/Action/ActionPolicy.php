<?php

namespace App\Policies\Action;

use \Tobuli\Entities\User;

abstract class ActionPolicy
{
    /* Check if user can perform action
    *
    * @param  User $user User model
    * @return Boolean
    */
    public abstract function able(User $user);
}
