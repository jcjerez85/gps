<?php

namespace Tobuli\Lookups\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class DeviceInactiveScope implements DataTableScope
{
    public function apply($query)
    {
        $minutes = settings('main_settings.default_object_inactive_timeout');

        return $query->offline($minutes);
    }
}