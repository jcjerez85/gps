<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tobuli\Traits\Searchable;

class DiemRate extends AbstractEntity
{
    use Searchable;

    const PERIOD_HOUR = 'h';
    const PERIOD_DAY = 'd';

    protected $fillable = [
        'title',
        'active',
        'rates',
    ];

    protected $searchable = [
        'title',
    ];

    protected $casts = [
        'rates' => 'array',
    ];

    public function geofence(): HasOne
    {
        return $this->hasOne(Geofence::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeAmountPerSecondAttribute(Builder $query): Builder
    {
        return $query->selectRaw('(diem_rates.amount / diem_rates.period / 3600) AS amount_per_second');
    }

    public function getAmountPerSecondAttribute()
    {
        return $this->amount / $this->period / 3600;
    }
}
