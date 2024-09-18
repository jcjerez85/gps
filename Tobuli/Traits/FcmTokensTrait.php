<?php

namespace Tobuli\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Tobuli\Entities\FcmToken;
use Tobuli\Entities\FcmTokenableInterface;

trait FcmTokensTrait
{
    public static function bootFcmTokensTrait()
    {
        static::deleting(function (FcmTokenableInterface $model) {
            $model->fcmTokens()->delete();
        });
    }

    public function fcmTokens(): MorphMany
    {
        return $this->morphMany(FcmToken::class, 'owner');
    }
}