<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class BraintreeConfigFormValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\BraintreeConfigFormValidator';
    }
}