<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class StripeConfigFormValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\StripeConfigFormValidator';
    }
}