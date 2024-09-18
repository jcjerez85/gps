<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tobuli\Traits\Filterable;
use Tobuli\Traits\Searchable;

class Company extends AbstractEntity
{
    use Searchable;
    use Filterable;

    protected $fillable = [
        'name',
        'registration_code',
        'vat_number',
        'address',
        'comment'
    ];

    protected $searchable = [
        'name',
        'address',
        'registration_code',
        'vat_number'
    ];

    protected $filterables = [
        'id',
        'name',
        'registration_code',
        'vat_number',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Company $company) {
            if ($company->owner && $company->owner->isAdmin()) {
                $company->owner()->dissociate();
            }
        });
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
}
