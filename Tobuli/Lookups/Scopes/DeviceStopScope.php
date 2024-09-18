<?php

namespace Tobuli\Lookups\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class DeviceStopScope implements DataTableScope
{
    public function apply($query)
    {
        return $query->stop();
    }
}