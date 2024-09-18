<?php

namespace Tobuli\Helpers\LbsLocation;

use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\LbsLocation\Service\LbsInterface;

class LbsManager
{
    const PROVIDERS = [
        'google'        => 'Google',
        'open_cell_id'  => 'Open Cell ID',
        'mozilla'       => 'Mozilla',
        'unwired_labs'  => 'Unwired Labs',
        'combain'       => 'Combain',
    ];

    public static function createService(string $provider, array $settings): LbsInterface
    {
        $class = 'Tobuli\Helpers\LbsLocation\Service\\' . str_replace('_', '', ucwords($provider, '_')) . 'Lbs';

        if (!class_exists($class, true)) {
            throw new ValidationException(['lbs_provider' => $provider . ' does not match any service']);
        }

        return new $class($settings);
    }
}