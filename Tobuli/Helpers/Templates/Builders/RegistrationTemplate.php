<?php

namespace Tobuli\Helpers\Templates\Builders;

class RegistrationTemplate extends TemplateBuilder
{
    /**
     * @param $item
     * @return array
     */
    protected function variables($item)
    {
        return [
            '[email]'    => $item->email,
            '[password]' => $item->password_to_email,
        ];
    }

    /**
     * @return array
     */
    protected function placeholders()
    {
        return [
            '[email]'    => 'User email',
            '[password]' => 'User password',
        ];
    }
}