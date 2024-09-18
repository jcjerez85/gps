<?php

namespace App\Handlers\Events;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Tobuli\Entities\SecondaryCredentialsInterface;
use Tobuli\Services\NotificationService;

class AuthLoginEventHandler {

    /**
     * Create the event handler.
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
     * @param  Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        session()->put('hash', $user->password_hash);

        $notificationService = new NotificationService();
        $notificationService->check($user);

        $this->writeSecondaryCredentials($user);
    }

    private function writeSecondaryCredentials(Authenticatable $user): void
    {
        if ($user instanceof SecondaryCredentialsInterface && $secondaryCred = $user->getLoginSecondaryCredentials()) {
            session()->put('secondary_cred_email', $secondaryCred->email);
            session()->put('secondary_cred_id', $secondaryCred->id);
        } else {
            session()->put('secondary_cred_email', false);
            session()->put('secondary_cred_id', false);
        }
    }

}