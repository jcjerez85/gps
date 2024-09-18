<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesInactiveLookupTable extends DevicesLookupTable
{
    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.inactive') . ")";
    }

    public function extraQuery($query)
    {
        $minutes = settings('main_settings.default_object_inactive_timeout');

        $query->offline($minutes);

        return parent::extraQuery($query);
    }
}