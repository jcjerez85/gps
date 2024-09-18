<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesMoveLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.move') . ")";
    }

    public function extraQuery($query)
    {
        $query->move();

        return parent::extraQuery($query);
    }
}