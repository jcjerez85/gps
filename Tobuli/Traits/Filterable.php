<?php namespace Tobuli\Traits;

use Illuminate\Support\Str;

trait Filterable {

    public function getFilters( $input )
    {
        $filters = [];

        if (empty($input))
            return $filters;

        if (empty($this->filterables))
            return $filters;

        foreach ($this->filterables as $filterable)
        {
            $key = str_replace('.', '_', $filterable);

            if ( ! array_key_exists($key, $input))
                continue;

            $filters[$filterable] = $input[$key];
        }

        return $filters;
    }

    public function scopeFilter( $query, $values )
    {
        $filters = $this->getFilters($values);

        foreach ($filters as $key => $value)
        {
            $method = "scopeFilter" . Str::camel($key);
            if (method_exists($this, $method)) {
                call_user_func([$this, $method], $query, $value);
                continue;
            }

            $parts = explode('.', $key);
            $relation = null;
            $field = $parts[0];

            if (isset($parts[1])) {
                $relation = $parts[0];
                $field = $parts[1];
            }

            if ($relation) {
                $key = $this->{$relation}()->getRelated()->getTable().'.'.$field;
                $query->whereHas($relation, function($query) use ($value, $key) {
                    $condition = is_array($value) ? 'whereIn' : 'where';
                    $query->{$condition}($key, $value);
                });
            } else {
                $condition = is_array($value) ? 'whereIn' : 'where';
                $query->{$condition}($this->table.'.'.$field, $value);
            }
        }

        return $query;
    }
}
