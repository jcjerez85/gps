<?php

namespace CustomFacades\Repositories;

use Illuminate\Support\Facades\Facade;

class SharingRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Repositories\Sharing\SharingRepositoryInterface';
    }
}
