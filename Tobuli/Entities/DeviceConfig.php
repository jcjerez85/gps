<?php

namespace Tobuli\Entities;

use Tobuli\Traits\Searchable;

class DeviceConfig extends AbstractEntity
{
    use Searchable;

    protected $table = 'device_config';

    protected $fillable = [
        'brand',
        'model',
        'commands',
        'edited',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'commands' => 'array',
    ];

    protected $searchable = [
        'brand',
        'model'
    ];

    public function getFullNameAttribute()
    {
        return trim($this->brand.' '.$this->model);
    }

    public function scopeNotEdited($query)
    {
        return $query->where('edited', 0);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
