<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class PaypalConfigFormValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\PaypalConfigFormValidator';
    }
}