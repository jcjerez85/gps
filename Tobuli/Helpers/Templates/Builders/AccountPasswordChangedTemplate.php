<?php

namespace Tobuli\Helpers\Templates\Builders;

class AccountPasswordChangedTemplate extends TemplateBuilder
{
    protected function variables($item): array
    {
        return [
            '[email]'    => $item['email'],
            '[password]' => $item['password']
        ];
    }

    protected function placeholders(): array
    {
        return [
            '[email]'    => 'User email',
            '[password]' => 'User password',
        ];
    }
}