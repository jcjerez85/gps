<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteGroup extends AbstractGroup
{
    protected $table = 'route_groups';

    protected $fillable = ['title', 'user_id', 'open'];

    public $timestamps = false;

    public function items()
    {
        return $this->routes();
    }

    public function itemsVisible()
    {
        return $this->routesVisible();
    }

    public function routes()
    {
        if (isset($this->id) && empty($this->id)) {
            return $this->hasMany(Route::class, 'user_id', 'user_id')->whereNull('group_id');
        }

        return $this->hasMany(Route::class, 'group_id');
    }

    public function routesVisible(): HasMany
    {
        return $this->routes()->where('active', 1);
    }
}
