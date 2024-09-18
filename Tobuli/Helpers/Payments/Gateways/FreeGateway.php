<?php

namespace Tobuli\Helpers\Payments\Gateways;

use Validator;
use App\Exceptions\PaymentsConfigurationException;
use App\Exceptions\PaymentsIssueException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Entities\Order;

class FreeGateway extends PaymentGateway implements PaymentGatewayInterface
{
    public function pay($user, Order $order)
    {
        if ( ! $order->plan->isFree())
            $this->handleException(new PaymentsIssueException('Plan not free'));

        do
        {
            $unique = md5(time());

            $validator = Validator::make(
                ['data' => $unique],
                ['data' => 'required|unique:subscriptions,gateway_id']
            );
        } while($validator->fails());

        $this->storeSubscription($user, $order, $unique);
        $this->activateSubscription($unique);

        return Redirect::route('payments.success');
    }

    public function payCallback(Request $request)
    {
        return Redirect::route('payments.success');
    }

    public function subscribe($user, Order $order)
    {
        return Redirect::route('payments.subscribe_callback', [
            'gateway'         => $this->gatewayName(),
            'subscription_id' => null,
        ]);
    }

    public function subscribeCallback(Request $request)
    {
        return Redirect::route('payments.success');
    }

    public function checkout(Order $order)
    {
        return view('front::Subscriptions.Gateways.free')->with([
            'order_id'      => $order->id,
            'gateway'       => $this->gatewayName(),
        ]);
    }

    public function isConfigCorrect(Request $request)
    {
        return true;
    }

    public function isSubscriptionActive($subscription)
    {
        return true;
    }

    public function isSubscriptionRenewed($subscription)
    {
        return true;
    }

    public function getSubscriptionEnd($subscription)
    {
        return $subscription->expiration_date;
    }

    public function cancelSubscription($subscription)
    {
        return true;
    }
}