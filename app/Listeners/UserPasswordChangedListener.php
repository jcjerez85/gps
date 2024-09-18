<?php

namespace App\Listeners;

use App\Events\UserPasswordChanged;
use Illuminate\Support\Facades\Auth;

class UserPasswordChangedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserPasswordChanged  $event
     * @return void
     */
    public function handle(UserPasswordChanged $event)
    {
        $user = $event->user;

        if (Auth::user() && Auth::user()->id == $user->id) {
            session()->put('hash', $user->password_hash);
        }
    }
}
