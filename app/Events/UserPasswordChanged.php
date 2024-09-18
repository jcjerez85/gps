<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\User;

class UserPasswordChanged
{
    use SerializesModels;

    public User $user;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}