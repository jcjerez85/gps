<?php


namespace Tobuli\Services\Commands;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection AS EloquentCollection;

interface DevicesCommands
{
    public function get(EloquentCollection $devices, bool $intersect) : Collection;
}