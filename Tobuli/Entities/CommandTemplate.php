<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tobuli\Traits\Filterable;
use Tobuli\Traits\Searchable;

class CommandTemplate extends AbstractEntity
{
    use Searchable;
    use Filterable;

    protected $table = 'command_templates';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'protocol',
        'adapted',
    ];

    protected $searchable = [
        'title',
        'message',
    ];

    protected $filterables = [
        'type',
        'protocol',
        'adapted',
        'user_id',
    ];

    public static function boot()
    {
        parent::boot();

        if (static::class !== CommandTemplate::class) {
            static::addGlobalScope('type', function (Builder $query) {
                $query->where('type', static::TYPE);
            });
        }

        static::creating(function (CommandTemplate $template) {
            if (get_class($template) !== CommandTemplate::class) {
                $template->forceFill(['type' => static::TYPE]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'command_template_devices', 'command_template_id', 'device_id');
    }

    public function deviceTypes(): BelongsToMany
    {
        return $this->belongsToMany(DeviceType::class, 'command_template_device_types', 'command_template_id', 'device_type_id');
    }

    public static function getAdapties(): array
    {
        return [
            '0'             => trans('front.all'),
            'protocol'      => trans('front.protocol'),
            'devices'       => trans('front.devices'),
            'device_types'  => trans('admin.device_types'),
        ];
    }

    public function getAdaptedTitleAttribute($value)
    {
        return self::getAdapties()[$this->adapted] ?? trans('front.all');
    }

    public function setProtocolAttribute($value)
    {
        $this->attributes['protocol'] = empty($value) ? null : $value;
    }

    public function setAdaptedAttribute($value)
    {
        $this->attributes['adapted'] = empty($value) ? null : $value;
    }

    public function isAdaptedFromDevice($device): bool
    {
        if (empty($this->adapted)) {
            return true;
        }

        if ($this->adapted == 'protocol' && $this->protocol == $device->protocol) {
            return true;
        }

        if ($this->adapted == 'devices' && $this->devices->contains('id', $device->id)) {
            return true;
        }

        if ($this->adapted == 'device_types' && $this->deviceTypes->contains('id', $device->device_type_id)) {
            return true;
        }

        return false;
    }

    public function scopeCommon(Builder $query): Builder
    {
        return $query->whereNull('user_id');
    }

    public function scopeUserOwned(Builder $query, User $user): Builder
    {
        return $query->where(['user_id' => $user->id]);
    }

    public function scopeUserAccessible(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user) {
            $query->userOwned($user);

            $query->orWhere(function (Builder $q) {
                $q->common();
            });
        });
    }

    public function scopeByDevices(Builder $query, Collection $devices, $strict = false): Builder
    {
        $query->where(function (Builder $q) use ($devices, $strict) {
            $q->whereNull('adapted');

            $q->orWhere(function($q) use ($devices, $strict) {
                $protocols = $devices->pluck('protocol')->unique()->all();
                $q->adaptedProtocols($protocols, $strict);
            });

            $q->orWhere(function($q) use ($devices, $strict) {
                $ids = $devices->pluck('id')->all();
                $q->adaptedDevices($ids, $strict);
            });

            $q->orWhere(function($q) use ($devices, $strict) {
                $ids = $devices->pluck('device_type_id')->unique()->all();
                $q->adaptedDeviceTypes($ids, $strict);
            });
        });

        return $query;
    }

    public function scopeAdaptedProtocols(Builder $query, array $protocols, $strict = false): Builder
    {
        $protocols = array_filter(array_unique($protocols));

        if (empty($protocols))
            return $query;

        $query->where('adapted', 'protocol');

        if ($strict && count($protocols) > 1) {
            //two different protocols cannot have the same template
            //we make a canceling condition
            $query->where('adapted', '!=', 'protocol');
        } else {
            $query->whereIn('protocol', $protocols);
        }

        return $query;
    }

    public function scopeAdaptedDevices(Builder $query, array $devices, $strict = false): Builder
    {
        if (empty($devices))
            return $query;

        $query->where('adapted', 'devices');

        $condition = $strict ? '=' : '>=';
        $count = $strict ? count($devices) : 1;

        $query->whereHas('devices', function ($q) use ($devices) {
            $q->whereIn('id', $devices);
        }, $condition, $count);

        return $query;
    }

    public function scopeAdaptedDeviceTypes(Builder $query, array $deviceTypes, $strict = false): Builder
    {
        $deviceTypes = array_filter(array_unique($deviceTypes));

        if (empty($deviceTypes))
            return $query;

        $query->where('adapted', 'device_types');

        $condition = $strict ? '=' : '>=';
        $count = $strict ? count($deviceTypes) : 1;

        $query->whereHas('deviceTypes', function ($q) use ($deviceTypes) {
            $q->whereIn('id', $deviceTypes);
        }, $condition, $count);

        return $query;
    }
}
