<?php

namespace App\Handlers\Events;

use Illuminate\Auth\Events\Logout;
use Tobuli\Entities\User;

class AuthLogoutEventHandler {

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
     * @param  Logout $event
     * @return void
     */
    public function handle(Logout $event)
    {
        session()->forget('hash');

        User::clearBootedModels();
    }

}