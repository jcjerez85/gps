<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class FcmToken extends AbstractEntity
{
    protected $table = 'fcm_tokens';

    protected $fillable = ['token'];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
