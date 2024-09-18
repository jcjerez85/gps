<?php namespace Tobuli\Entities;

class DeviceRouteType extends AbstractEntity
{
    const TYPE_NONE = 0;
    const TYPE_BUSINESS = 1;
    const TYPE_PRIVATE = 2;

    protected $table = 'device_route_types';

    protected $fillable = [
        'user_id',
        'device_id',
        'started_at',
        'ended_at',
        'type',
    ];

    public static function types()
    {
        return [
            self::TYPE_NONE     => trans('front.not_available'),
            self::TYPE_PRIVATE  => trans('front.private'),
            self::TYPE_BUSINESS => trans('front.business'),
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function getTypeTitleAttribute()
    {
        return self::types()[$this->type] ?? null;
    }

}
