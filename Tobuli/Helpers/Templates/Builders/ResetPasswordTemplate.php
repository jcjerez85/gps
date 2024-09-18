<?php

namespace Tobuli\Helpers\Templates\Builders;

class ResetPasswordTemplate extends TemplateBuilder
{
    /**
     * @param string $token
     * @return array
     */
    protected function variables($token): array
    {
        return [
            '[url]' => route('password.reset', $token, true),
        ];
    }

    protected function placeholders()
    {
        return [
            '[url]' => 'Password reset url link',
        ];
    }
}