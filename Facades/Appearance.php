<?php

namespace CustomFacades;

use Illuminate\Support\Facades\Facade;
use Tobuli\Services\AppearanceService;

class Appearance extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return AppearanceService::class;
    }
}
