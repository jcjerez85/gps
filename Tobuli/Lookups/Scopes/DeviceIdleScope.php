<?php

namespace Tobuli\Lookups\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class DeviceIdleScope implements DataTableScope
{
    public function apply($query)
    {
        return $query->idle();
    }
}