<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends AbstractEntity
{
    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'personal_code',
        'address',
        'comment',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
