<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class DeviceExpensesFormValidator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\DeviceExpensesFormValidator';
    }
}