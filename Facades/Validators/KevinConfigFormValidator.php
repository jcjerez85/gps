<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class KevinConfigFormValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Tobuli\Validation\KevinConfigFormValidator::class;
    }
}