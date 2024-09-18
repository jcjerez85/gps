<?php

namespace Tobuli\Lookups\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class DeviceNeverConnectedScope implements DataTableScope
{
    public function apply($query)
    {
        return $query->neverConnected();
    }
}