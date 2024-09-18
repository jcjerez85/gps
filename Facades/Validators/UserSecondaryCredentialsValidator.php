<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class UserSecondaryCredentialsValidator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Tobuli\Validation\UserSecondaryCredentialsValidator::class;
    }
}