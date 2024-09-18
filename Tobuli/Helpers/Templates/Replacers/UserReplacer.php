<?php

namespace Tobuli\Helpers\Templates\Replacers;

use Tobuli\Entities\Device;

class UserReplacer extends Replacer
{
    /**
     * @param Device $device
     * @return array
     */
    public function replacers($device)
    {
        $list = [
            'email',
        ];

        return $this->formatFields($device, $list);
    }

    /**
     * @return array
     */
    public function placeholders()
    {
        return [
            $this->formatKey('email') => 'User email',
        ];
    }
}