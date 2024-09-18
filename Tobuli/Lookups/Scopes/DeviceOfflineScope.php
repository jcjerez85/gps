<?php

namespace Tobuli\Lookups\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class DeviceOfflineScope implements DataTableScope
{
    public function apply($query)
    {
        return $query->offline();
    }
}