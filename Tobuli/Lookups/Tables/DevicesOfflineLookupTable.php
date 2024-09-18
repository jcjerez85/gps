<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesOfflineLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.offline') . ")";
    }

    public function extraQuery($query)
    {
        $query->offline();

        return parent::extraQuery($query);
    }
}