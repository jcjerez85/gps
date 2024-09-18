<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class MediaCategoryValidator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Tobuli\Validation\MediaCategoryValidator::class;
    }
}