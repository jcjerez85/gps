<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\User;

class UserFirstLoginEvent extends Event
{
    use SerializesModels;

    public $user;

    public function __construct(User $user) {
        $this->user = $user;
    }
}
