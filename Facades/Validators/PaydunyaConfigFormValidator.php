<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class PaydunyaConfigFormValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\PaydunyaConfigFormValidator';
    }
}