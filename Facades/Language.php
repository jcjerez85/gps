<?php

namespace CustomFacades;

use Illuminate\Support\Facades\Facade;

class Language extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Tobuli\Helpers\Language::class;
    }
}