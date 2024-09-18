<?php

namespace Tobuli\Helpers\Payments\Gateways;

use Illuminate\Http\Request;
use Tobuli\Entities\Order;

interface PaymentGatewayInterface
{
    public function pay($user, Order $order);

    public function payCallback(Request $request);

    public function subscribe($user, Order $order);

    public function subscribeCallback(Request $request);

    public function checkout(Order $order);

    public function isConfigCorrect(Request $request);

    public function isSubscriptionActive($subscription);

    public function cancelSubscription($subscription);
}