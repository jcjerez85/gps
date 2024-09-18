<?php

namespace Tobuli\Exporters\EntityManager\Poi\Filter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Exporters\Util\FilterInterface;

class SinglesPoiFilter implements FilterInterface
{
    public function applyFilter(Builder $query, array $data): Builder
    {
        $ids = $data['pois'] ?? [];

        if (\count($ids)) {
            $query->whereIn('id', $data['pois']);
        }

        return $query;
    }
}
