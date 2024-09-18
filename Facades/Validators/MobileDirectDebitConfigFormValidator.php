<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class MobileDirectDebitConfigFormValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\MobileDirectDebitConfigFormValidator';
    }
}