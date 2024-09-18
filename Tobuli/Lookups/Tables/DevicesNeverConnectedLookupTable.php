<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesNeverConnectedLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.never_connected') . ")";
    }

    public function extraQuery($query)
    {
        $query->neverConnected();

        return parent::extraQuery($query);
    }
}