<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\AbstractSidebarItemsController;
use Tobuli\Entities\PoiGroup;

class PoisSidebarController extends AbstractSidebarItemsController
{
    protected string $repo = 'poi';
    protected string $viewDir = 'front::Pois';
    protected string $nextRoute = 'pois.sidebar';
    protected string $groupClass = PoiGroup::class;
}
