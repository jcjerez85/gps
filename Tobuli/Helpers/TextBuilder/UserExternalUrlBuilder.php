<?php

namespace Tobuli\Helpers\TextBuilder;

use Tobuli\Entities\User;

class UserExternalUrlBuilder extends AbstractTextBuilder
{
    /**
     * @param User $args
     * @return array
     */
    protected function getValueMap($args): array
    {
        return [
            '%ID%' => $args->id,
            '%EMAIL%' => $args->email,
            '%REMEMBER_TOKEN%' => $args->getRememberToken(),
        ];
    }
}