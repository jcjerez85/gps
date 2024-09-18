<?php

namespace Tobuli\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GodUserScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('users.email', '!=', 'admin@server.com');
    }

    public function remove(Builder $builder, Model $model)
    {
        return $builder;
    }
}