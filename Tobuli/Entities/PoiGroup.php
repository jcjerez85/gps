<?php
namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\HasMany;

class PoiGroup extends AbstractGroup
{
    protected $table = 'poi_groups';

    protected $fillable = ['title', 'user_id', 'open'];

    public $timestamps = false;

    public function items()
    {
        return $this->pois();
    }

    public function itemsVisible()
    {
        return $this->poisVisible();
    }

    public function pois(): HasMany
    {
        if (isset($this->id) && empty($this->id)) {
            return $this->hasMany(Poi::class, 'user_id', 'user_id')->whereNull('group_id');
        }

        return $this->hasMany(Poi::class, 'group_id');
    }

    public function poisVisible(): HasMany
    {
        return $this->pois()->where('active', 1);
    }
}
