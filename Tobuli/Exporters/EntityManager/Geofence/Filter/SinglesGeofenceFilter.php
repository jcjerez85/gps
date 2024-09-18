<?php

namespace Tobuli\Exporters\EntityManager\Geofence\Filter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Exporters\Util\FilterInterface;

class SinglesGeofenceFilter implements FilterInterface
{
    public function applyFilter(Builder $query, array $data): Builder
    {
        $ids = $data['geofences'] ?? [];

        if (\count($ids)) {
            $query->whereIn('id', $data['geofences']);
        }

        return $query;
    }
}
