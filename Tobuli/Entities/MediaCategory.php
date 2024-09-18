<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tobuli\Traits\Searchable;

class MediaCategory extends AbstractEntity
{
    use Searchable;

    protected $table = 'media_categories';

    protected $fillable = [
        'title',
        'user_id',
    ];

    protected $searchable = ['title', 'user.email'];

    public $timestamps = false;

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
        return $query->where(function (Builder $query) use ($user) {
            $query->userOwned($user);
            $query->orWhereNull('user_id');
        });
    }

    public function scopeUsersAccessible(Builder $query, $users): Builder
    {
        return $query->where(function (Builder $query) use ($users) {
            foreach ($users as $user) {
                $query->orWhere(function (Builder $query) use ($user) {
                    $query->userAccessible($user);
                });
            }
        });
    }
}
