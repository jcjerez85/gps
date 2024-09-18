<?php

namespace Tobuli\Entities;

use App\Events\UserSecondaryCredentialsPasswordChanged;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Tobuli\Traits\Searchable;

class UserSecondaryCredentials extends AbstractEntity
{
    use Searchable;

    protected $table = 'user_secondary_credentials';

    protected $fillable = ['user_id', 'password', 'email'];

    protected $hidden = ['password', 'api_hash'];

    protected $searchable = ['email'];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::saving(function (UserSecondaryCredentials $cred) {
            if ($cred->isDirty('password')) {
                while (self::where([
                    'api_hash' => $hash = Hash::make("{$cred->email}:{$cred->password}")
                ])->first()) {
                }

                $cred->api_hash = $hash;
            }
        });

        static::updated(function (UserSecondaryCredentials $cred) {
            \Cache::forget('secondary_cred_' . $cred->id);
        });

        static::deleted(function (UserSecondaryCredentials $cred) {
            \Cache::forget('secondary_cred_' . $cred->id);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUserOwned(Builder $query, User $user): Builder
    {
        return $query->where(['user_id' => $user->id]);
    }

    public function scopeUserAccessible(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isSupervisor()) {
            return $query;
        }

        if ($user->isManager()) {
            return $query->where(function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereIn('user_id', function (\Illuminate\Database\Query\Builder $query) use ($user) {
                        $query->select('id')->from('users')->where('manager_id', $user->id);
                    });
            });
        }

        return $query->where('user_id', $user->id);
    }

    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        $this->attributes['password'] = Hash::make($value);

        event(new UserSecondaryCredentialsPasswordChanged($this));
    }
}
