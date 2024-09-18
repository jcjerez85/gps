<?php
namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\HasMany;

class GeofenceGroup extends AbstractGroup
{
    protected $table = 'geofence_groups';

    protected $fillable = ['title', 'user_id', 'open'];

    public $timestamps = false;

    public function items()
    {
        return $this->geofences();
    }

    public function itemsVisible()
    {
        return $this->geofencesVisible();
    }

    public function geofences()
    {
        if (isset($this->id) && empty($this->id)) {
            return $this->hasMany(Geofence::class, 'user_id', 'user_id')->whereNull('group_id');
        }

        return $this->hasMany(Geofence::class, 'group_id');
    }

    public function geofencesVisible(): HasMany
    {
        return $this->geofences()->where('active', 1);
    }
}
