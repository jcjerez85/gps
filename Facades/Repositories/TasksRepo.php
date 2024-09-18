<?php

namespace CustomFacades\Repositories;

use Illuminate\Support\Facades\Facade;

class TasksRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Repositories\Tasks\TasksRepositoryInterface';
    }
}