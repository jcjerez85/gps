<?php namespace Tobuli\Entities;


use Tobuli\Traits\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DeviceGroup extends AbstractGroup
{
    use Searchable;

	protected $table = 'device_groups';

    protected $fillable = ['title', 'user_id', 'open'];

    public $timestamps = false;

    public function items()
    {
        return $this->devices();
    }

    public function itemsVisible()
    {
        return $this->devicesVisible();
    }

    public function devices(): BelongsToMany
    {
        $devices = $this->belongsToMany(Device::class, 'user_device_pivot', 'group_id', 'device_id');

        if (isset($this->user_id))
            $devices->where('user_device_pivot.user_id', $this->user_id);

        return $devices;
    }

    public function devicesVisible(): BelongsToMany
    {
        return $this->devices()->where('user_device_pivot.active', true);
    }

    public function forwards(): BelongsToMany
    {
        return $this->belongsToMany(Forward::class, 'device_group_forward', 'group_id', 'forward_id');
    }
}
