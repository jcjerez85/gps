<?php

namespace CustomFacades\Validators;

use Illuminate\Support\Facades\Facade;

class TasksFormValidator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Validation\TasksFormValidator';
    }
}