<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesIdleLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.idle') . ")";
    }

    public function extraQuery($query)
    {
        $query->idle();

        return parent::extraQuery($query);
    }
}