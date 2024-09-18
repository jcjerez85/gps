<?php

namespace Tobuli\Helpers\Templates\Replacers;

use Tobuli\Entities\UserDriver;

class DriverReplacer extends Replacer
{
    /**
     * @param UserDriver $driver
     * @return array
     */
    public function replacers($driver)
    {
        $list = [
            'name',
            'rfid',
            'phone',
            'email',
        ];

        return $this->formatFields($driver, $list);
    }

    /**
     * @return array
     */
    public function placeholders()
    {
        return [
            $this->formatKey('name') => 'Driver name',
            $this->formatKey('rfid') => 'Driver RFID',
            $this->formatKey('phone') => 'Driver phone',
            $this->formatKey('email') => 'Driver email',
        ];
    }
}