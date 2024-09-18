<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesExpiredLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.expired') . ")";
    }

    public function extraQuery($query)
    {
        $query->expired();

        return parent::extraQuery($query);
    }
}