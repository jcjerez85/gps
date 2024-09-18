<?php

namespace Tobuli\Helpers\Templates\Builders;

use Tobuli\Entities\User;

class ExpiredUserTemplate extends TemplateBuilder
{

    /**
     * @param User $user
     * @return array
     */
    protected function variables($user)
    {
        return [
            '[email]' => $user->email,
            '[days]'  => settings('main_settings.expire_notification.days_after'),
        ];
    }

    protected function placeholders()
    {
        return [
            '[email]' => 'User email',
            '[days]'  => 'Days after expiration',
        ];
    }
}