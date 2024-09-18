<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class CallActionFormValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\CallActionFormValidator';
    }
}
