<?php

namespace App\Listeners;

use App\Events\UserSecondaryCredentialsPasswordChanged;
use Illuminate\Support\Facades\Auth;
use Tobuli\Entities\SecondaryCredentialsInterface;

class UserSecondaryCredentialsPasswordChangedListener
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
     * @param  UserSecondaryCredentialsPasswordChanged  $event
     * @return void
     */
    public function handle(UserSecondaryCredentialsPasswordChanged $event)
    {
        if (!Auth::user() instanceof SecondaryCredentialsInterface) {
            return;
        }

        $cred = $event->credentials;

        if (!$cred->exists) {
            return;
        }

        $userSecondaryCredId = Auth::user()->getLoginSecondaryCredentials()->id ?? null;

        if ($userSecondaryCredId === $cred->id) {
            Auth::user()->setLoginSecondaryCredentials($cred);

            session()->put('hash', Auth::user()->password_hash);
        }
    }
}
