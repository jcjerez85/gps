<?php

namespace CustomFacades\Repositories;

use Illuminate\Support\Facades\Facade;

class PoiGroupRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tobuli\Repositories\PoiGroup\PoiGroupRepositoryInterface';
    }
}