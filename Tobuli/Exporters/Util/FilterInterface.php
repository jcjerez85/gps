<?php

namespace Tobuli\Exporters\Util;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    public function applyFilter(Builder $query, array $data): Builder;
}