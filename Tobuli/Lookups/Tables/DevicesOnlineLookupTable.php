<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesOnlineLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('global.online') . ")";
    }

    public function extraQuery($query)
    {
        $query->online();

        return parent::extraQuery($query);
    }
}