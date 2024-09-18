<?php

namespace Tobuli\Helpers\Payments\Gateways;


use App\Exceptions\PaymentsConfigurationException;
use App\Exceptions\PaymentsUnavailableException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Helpers\Payments\Gateways\Paydunya\Checkout\CheckoutInvoice;
use Tobuli\Helpers\Payments\Gateways\Paydunya\Checkout\Store;
use Tobuli\Helpers\Payments\Gateways\Paydunya\Setup;
use Tobuli\Entities\Order;

class PaydunyaGateway extends PaymentGateway implements PaymentGatewayInterface
{
    private $config;

    public function __construct()
    {
        $this->config = settings('payments.paydunya');
        $this->setup();

        parent::__construct();
    }

    public function pay($user, Order $order)
    {
        $payment = $this->loadPayment();
        $payment->addItem($order->plan->title, 1, $order->plan->price, $order->plan->price);
        $payment->setTotalAmount($order->plan->price);

        if ( ! $payment->create()) {
            // any other case are handled inside payment window
            $this->handleException(new PaymentsUnavailableException());
        }

        $this->storeSubscription($user, $order, $payment->token);

        return Redirect::away($payment->getInvoiceUrl());
    }

    public function payCallback(Request $request)
    {
        $token = $request->token;

        if ( ! (new CheckoutInvoice)->confirm($token)) {
            $this->handleException(new PaymentsUnavailableException());
        }

        $this->activateSubscription($token);

        return Redirect::route('payments.success');
    }

    public function checkout(Order $order)
    {
        return Redirect::route('payments.pay', [
            'order_id'      => $order->id,
            'gateway'       => $this->gatewayName(),
        ]);
    }

    public function isConfigCorrect(Request $request)
    {
        $this->config = array_replace($this->config, $request->except('_token'));
        $this->setup();

        $payment = $this->loadPayment();
        $payment->addItem('Check config correct', 1, 200, 200);
        $payment->setTotalAmount(200);

        if ( ! $payment->create()) {
            $this->handleException(new PaymentsConfigurationException($payment->response_text));
        }

        return true;
    }

    private function setup()
    {
        Setup::setMasterKey($this->config['master_key']);
        Setup::setPublicKey($this->config['public_key']);
        Setup::setPrivateKey($this->config['private_key']);
        Setup::setToken($this->config['token']);
        Setup::setMode($this->config['mode']);
    }

    private function loadPayment()
    {
        Store::setCancelUrl(route('payments.cancel'));
        Store::setReturnUrl(route('payments.pay_callback', ['gateway' => $this->gatewayName()]));
        Store::setName($this->config['payment_name']);

        return new CheckoutInvoice();
    }

    // Subscriptions not supported.
    public function subscribe($user, Order $order) {}
    public function subscribeCallback(Request $request) {}
    public function isSubscriptionActive($subscription) {}
    public function cancelSubscription($subscription) {}
}