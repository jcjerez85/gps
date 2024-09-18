<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\AbstractSidebarItemsController;
use Tobuli\Entities\GeofenceGroup;

class GeofencesSidebarController extends AbstractSidebarItemsController
{
    protected string $repo = 'geofences';
    protected string $viewDir = 'front::Geofences';
    protected string $nextRoute = 'geofences.sidebar';
    protected string $groupClass = GeofenceGroup::class;
}
