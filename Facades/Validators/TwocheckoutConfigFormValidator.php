<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class TwocheckoutConfigFormValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Tobuli\Validation\TwocheckoutConfigFormValidator::class;
    }
}