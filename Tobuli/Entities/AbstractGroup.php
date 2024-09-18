<?php namespace Tobuli\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Tobuli\Traits\Searchable;

abstract class AbstractGroup extends AbstractEntity
{
    use Searchable;

    protected array $searchable = [
        'title'
    ];

    /**
     * @return BelongsToMany|HasMany
     */
    abstract public function items();

    /**
     * @return BelongsToMany|HasMany
     */
    abstract public function itemsVisible();

    /**
     * @return static
     */
    public static function makeUngrouped(User $user = null)
    {
        $instance = new static([
            'title' => trans('front.ungrouped'),
            'open' => $user->ungrouped_open[static::keyUngrouped()] ?? false,
            'user_id' => $user->id ?? null,
        ]);
        $instance->id = 0;

        return $instance;
    }

    /**
     * @return static
     */
    public static function makeUngroupedWithCount(User $user = null)
    {
        $instance = self::makeUngrouped($user);

        $instance->items_count = $instance->items()->count();

        $instance->items_visible_count = $instance->items_count
            ? $instance->itemsVisible()->count()
            : 0;

        return $instance;
    }

    public static function keyUngrouped(): string
    {
        return Str::snake(class_basename(static::class));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUserOwned(Builder $query, User $user): Builder
    {
        return $query->where(['user_id' => $user->id]);
    }
}
