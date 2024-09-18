<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\User;

class ChatPolicy extends Policy
{
    protected $permisionKey = null;

    protected function ownership(User $user, Model $entity = null)
    {
        if ( ! $entity->participants)
            return false;

        foreach ($entity->participants as $participant)
        {
            if ( ! $participant->isUser())
                continue;

            if ($participant->chattable_id == $user->id)
                return true;
        }

        return false;
    }
}
