<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\AbstractSidebarItemsController;
use Tobuli\Entities\RouteGroup;

class RoutesSidebarController extends AbstractSidebarItemsController
{
    protected string $repo = 'routes';
    protected string $viewDir = 'front::Routes';
    protected string $nextRoute = 'routes.sidebar';
    protected string $groupClass = RouteGroup::class;
}
