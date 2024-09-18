<?php namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Traits\Searchable;

class UserDriver extends AbstractEntity {

    use Searchable;

	protected $table = 'user_drivers';

    protected $fillable = array(
        'user_id',
        'device_id',
        'name',
        'rfid',
        'phone',
        'email',
        'description'
    );

    protected $searchable = [
        'name',
        'rfid',
        'phone',
        'email',
    ];

    public function user() {
        return $this->belongsTo('Tobuli\Entities\User', 'user_id', 'id');
    }

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'id', 'device_id');
    }

    public function devices() {
        return $this->belongsToMany('Tobuli\Entities\Device', 'user_driver_devices', 'driver_id', 'device_id');
    }

    public function getNameWithRfidAttribute()
    {
        return $this->name . (empty($this->rfid) ? "" : " ({$this->rfid})");
    }

    public function setDeviceIdAttribute($value)
    {
        $this->attributes['device_id'] = empty($value) ? null : $value;
    }

    public function changeDevice($device, $time = null, $withoutAlerts = false)
    {
        if (is_null($time))
            $time = date('Y-m-d H:i:s');

        if ($currentDevice = $this->device) {
            $currentDevice->current_driver_id = null;
            $currentDevice->save();
        }

        if ($device) {
            $device->changeDriver($this, $time, $withoutAlerts);
        }

        $this->device_id = $device->id ?? null;
        $this->save();
    }

    public function scopeUserOwned(Builder $query, User $user): Builder
    {
        return $query->where(['user_id' => $user->id]);
    }

    public function scopeUserAccessible(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user) {
            $query->userOwned($user);
        });
    }
}
