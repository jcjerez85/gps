<?php namespace Tobuli\Entities;

use App\Exceptions\ResourseNotFoundException;
use Tobuli\Helpers\Payments\Payments;
use Exception;
use Carbon;
use Tobuli\Traits\Searchable;

class Subscription extends AbstractEntity
{
    use Searchable;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'gateway',
        'gateway_id',
        'order_id',
        'expiration_date',
        'active'
    ];

    protected $searchable = [
        'user_id',
        'gateway',
        'gateway_id',
        'expiration_date',
    ];

    public function user()
    {
        return $this->hasOne('Tobuli\Entities\User', 'id', 'user_id');
    }

    public function order()
    {
        return $this->belongsTo('Tobuli\Entities\Order', 'order_id', 'id');
    }

    public function scopeSubscribable($query)
    {
        $not_subscribable = ['paydunya'];

        return $query->whereNotIn('gateway', $not_subscribable);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function cancel()
    {
        $payments = new Payments($this->gateway);

        $payments->cancelSubscription($this);

        $this->update(['active' => 0]);
    }

    public function activateEntity($options = [])
    {
        $entity = $this->order->entity;

        if (! $entity) {
            throw new ResourseNotFoundException(trans('global.'.$this->order->plan_type));
        }

        $plan = $this->order->plan;

        if (! $plan) {
            throw new ResourseNotFoundException(trans('admin.billing_plan'));
        }

        $expirationDate = $this->calculateExpirationDate();
        $entity->activate($plan, $expirationDate);

        $this->update([
            'active'          => true,
            'expiration_date' => $expirationDate,
        ] + $options);

        $this->order->update([
            'paid_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function renew($expirationDate = null)
    {
        if (is_null($expirationDate)) {
            $expirationDate = $this->calculateExpirationDate();
        }

        if (! $this->isValidExpirationDate($expirationDate)) {
            throw new Exception('Invalid expiration date "'.$expirationDate.'"');
        }

        $this->order->entity->renew($expirationDate);

        $this->update([
            'active'          => true,
            'expiration_date' => $expirationDate,
        ]);
    }

    public function setExpirationDate($expirationDate)
    {
        $this->order->entity->setExpirationDate($expirationDate);

        $this->update([
            'expiration_date' => $expirationDate,
        ]);
    }

    private function isValidExpirationDate($expirationDate)
    {
        if (strtotime($expirationDate) === false) {
            return false;
        }

        $date = Carbon::parse($expirationDate);

        return $date->gt(Carbon::today())
            && Carbon::parse($this->expiration_date)->diffInDays($date, false) > -2;
    }

    private function calculateExpirationDate()
    {
        $entity = $this->order->entity;
        $plan = $this->order->plan;

        if (is_null($entity) || is_null($plan)) {
            throw new \Exception('Subscription not found for activation!');
        }

        if (! $entity->isExpiredWithoutExtra() && $entity->plan_id == $plan->id) {
            $startDate = Carbon::parse($entity->expiration_date);
        } else {
            $startDate = Carbon::now();
        }

        return $plan->calculateExpirationDate($startDate);
    }
}
