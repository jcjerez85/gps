<?php namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;
use Tobuli\Traits\Orderable;

class DevicePlan extends AbstractEntity
{
    use Orderable;

    protected $table = 'device_plans';

    protected $fillable = [
        'title',
        'price',
        'duration_type',
        'duration_value',
        'description',
        'active'
    ];

    public function deviceTypes()
    {
        return $this->belongsToMany(DeviceType::class);
    }

    public function getDurationTextAttribute()
    {
        return $this->duration_value.' '.self::getDurationType($this->duration_type);
    }

    public static function getDurationTypes()
    {
        return [
            'days' => trans('front.days'),
            'months' => trans('front.months'),
            'years' => trans('front.years'),
        ];
    }

    public static function getDurationType($type)
    {
        return self::getDurationTypes()[$type] ?? null;
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeForDevice($query, Device $device)
    {
        $query->where(function(Builder $q) use ($device) {
            $q->whereHas('deviceTypes', function (Builder $q) use ($device) {
                $q->where('id', $device->device_type_id);
            });
            $q->orWhereDoesntHave('deviceTypes');
        });
    }

    public function formatDuration()
    {
        if ($this->duration_value > 1)
            return $this->duration_value . " " . self::getDurationType($this->duration_type);

        switch ($this->duration_type) {
            case 'days':
                return trans('front.daily');
            case 'months':
                return trans('front.monthly');
            case 'years':
                return trans('front.yearly');
            default:
                return null;
        }
    }

    public function formatPriceDuration()
    {
        return settings('currency.symbol') . $this->price . ' / ' . $this->formatDuration();
    }
}
