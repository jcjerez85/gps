<?php

namespace Tobuli\Exporters\EntityManager\Route\Filter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Exporters\Util\FilterInterface;

class SinglesRouteFilter implements FilterInterface
{
    public function applyFilter(Builder $query, array $data): Builder
    {
        $ids = $data['routes'] ?? [];

        if (\count($ids)) {
            $query->whereIn('id', $data['routes']);
        }

        return $query;
    }
}
