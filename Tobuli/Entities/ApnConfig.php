<?php

namespace Tobuli\Entities;

use Tobuli\Traits\Searchable;

class ApnConfig extends AbstractEntity
{
    use Searchable;

    protected $table = 'apn_config';

    protected $fillable = [
        'name',
        'apn_name',
        'apn_username',
        'apn_password',
        'edited',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $searchable = [
        'name',
        'apn_name'
    ];

    public function scopeNotEdited($query)
    {
        return $query->where('edited', 0);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
