<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\UserSecondaryCredentials;

class UserSecondaryCredentialsPasswordChanged
{
    use SerializesModels;

    public UserSecondaryCredentials $credentials;

    /**
     * Create a new event instance.
     *
     * @param UserSecondaryCredentials $credentials
     * @return void
     */
    public function __construct(UserSecondaryCredentials $credentials)
    {
        $this->credentials = $credentials;
    }
}