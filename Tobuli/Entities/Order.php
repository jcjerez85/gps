<?php namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\Relation;

class Order extends AbstractEntity
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_type',
        'entity_id',
        'entity_type',
        'paid_at',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo('\Tobuli\Entities\User', 'user_id', 'id');
    }

    public function plan()
    {
        $class = Relation::morphMap()[$this->plan_type] ?? null;

        if (is_null($class)) {
            return null;
        }

        return $this->hasOne($class, 'id', 'plan_id');
    }

    public function entity()
    {
        $class = Relation::morphMap()[$this->entity_type] ?? null;

        if (is_null($class)) {
            return null;
        }

        return $this->hasOne($class, 'id', 'entity_id');
    }

    public function getPrice()
    {
        return $this->plan->price;
    }

    public static function getPlanTypes()
    {
        return [
            'device_plan'  => DevicePlan::class,
            'billing_plan' => BillingPlan::class,
        ];
    }

    public static function getPlanByType($type)
    {
        return self::getPlanTypes()[$type] ?? null;
    }

    public static function getEntityTypes()
    {
        return [
            'device' => Device::class,
            'user'   => User::class,
        ];
    }

    public static function getEntityType($type)
    {
        return self::getEntityTypes()[$type] ?? null;
    }

    public function isPaid()
    {
        return !is_null($this->paid_at);
    }
}
