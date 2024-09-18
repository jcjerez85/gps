<?php namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\BillingPlan;
use Tobuli\Entities\Order;


class SubscriptionsController extends Controller
{
    public function index()
    {
        $user = $this->user;

        $time = new DateTime(date('Y-m-d H:i:s'));

        $days_left = $time->diff(new DateTime($user->subscription_expiration))->days;

        return View::make('front::Subscriptions.index')->with(compact('user', 'days_left'));
    }

    public function renew()
    {
        $plan = BillingPlan::findOrFail($this->user->billing_plan_id);

        $order = Order::create([
            'user_id'     => $this->user->id,
            'plan_id'     => $plan->id,
            'plan_type'   => 'billing_plan',
            'price'       => $plan->price,
            'entity_id'   => $this->user->id,
            'entity_type' => 'user',
        ]);

        return Redirect::route('payments.gateways', ['order_id' => $order->id]);
    }
}