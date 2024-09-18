<?php

namespace Tobuli\Helpers;

use Illuminate\Support\Arr;

class Password
{
    const CHARS_LISTS = [
        'uppercase' => 'ABCDEFGHIKLMNOPQRSTVXYZ',
        'lowercase' => 'abcdefghijklmnopqrstuvwxyz',
        'numbers'   => '0123456789',
        'specials'   => '!@#$%?'
    ];

    /**
     * @throws \Exception
     */
    public static function generate(int $length = null, array $charList = null): string
    {
        if (is_null($length)) {
            $length = settings('password.length');
        }

        if ($length < $minLength = settings('password.min_length')) {
            throw new \Exception("Length must be more than $minLength");
        }

        $chars = implode('', Arr::only(self::CHARS_LISTS, $charList ?: settings('password.includes')));

        $password = '';

        for ($i = 0, $interval = strlen($chars) - 1; $i < $length; $i++) {
            $password .= $chars[rand(0, $interval)];
        }

        return $password;
    }
}