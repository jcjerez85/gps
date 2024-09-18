<?php

namespace Tobuli\Lookups\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class DeviceMoveScope implements DataTableScope
{
    public function apply($query)
    {
        return $query->move();
    }
}