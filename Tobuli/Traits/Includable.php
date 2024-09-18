<?php namespace Tobuli\Traits;

trait Includable {
    public function scopeIncludes( $query, $values )
    {
        $includes = is_string($values) ? explode(',', $values) : $values;
        $includes = is_array($includes) ? $includes : [$includes];

        foreach ($includes as $include)
        {
            if (empty($include))
                continue;

            if ( ! $this->hasRelation($include))
                continue;

            $query->with($include);
        }

        return $query;
    }

    protected function hasRelation($key)
    {
        if (method_exists($this, $key)) {
            return is_a($this->$key(), 'Illuminate\Database\Eloquent\Relations\Relation');
        }

        return false;
    }
}
