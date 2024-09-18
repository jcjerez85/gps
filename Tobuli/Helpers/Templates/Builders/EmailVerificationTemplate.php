<?php

namespace Tobuli\Helpers\Templates\Builders;

class EmailVerificationTemplate extends TemplateBuilder
{
    protected function variables($token): array
    {
        return [
            '[link]' => route('verification.verify', $token),
        ];
    }

    protected function placeholders(): array
    {
        return [
            '[link]'  => 'Verification link',
        ];
    }
}