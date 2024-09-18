<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesParkLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.stop') . ")";
    }

    public function extraQuery($query)
    {
        $query->park();

        return parent::extraQuery($query);
    }
}