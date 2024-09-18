<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface FcmTokenableInterface
{
    public function fcmTokens(): MorphMany;
}