<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesStopLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.stop') . ")";
    }

    public function extraQuery($query)
    {
        $query->stop();

        return parent::extraQuery($query);
    }
}