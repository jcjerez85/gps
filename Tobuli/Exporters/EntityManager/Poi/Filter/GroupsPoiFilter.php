<?php

namespace Tobuli\Exporters\EntityManager\Poi\Filter;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Exporters\Util\FilterInterface;

class GroupsPoiFilter implements FilterInterface
{
    public function applyFilter(Builder $query, array $data): Builder
    {
        $groups = $data['groups'] ?? [];

        $query->where(function ($query) use ($groups) {
            $query->whereIn('group_id', $groups);

            if (in_array('0', $groups)) {
                $query->orWhereNull('group_id');
            }
        });

        return $query;
    }
}
