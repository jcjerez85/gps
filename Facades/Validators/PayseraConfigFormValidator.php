<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class PayseraConfigFormValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Tobuli\Validation\PayseraConfigFormValidator::class;
    }
}