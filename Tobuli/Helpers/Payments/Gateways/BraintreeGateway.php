<?php

namespace Tobuli\Helpers\Payments\Gateways;


use App\Exceptions\PaymentsConfigurationException;
use App\Exceptions\PaymentsIssueException;
use App\Exceptions\PaymentsUnavailableException;
use Braintree\Transaction;
use Braintree_Configuration;
use Braintree_Gateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Entities\Order;


class BraintreeGateway extends PaymentGateway implements PaymentGatewayInterface
{
    const transactionSuccessStatuses = [
        Transaction::AUTHORIZED,
        Transaction::AUTHORIZING,
        Transaction::SETTLED,
        Transaction::SETTLING,
        Transaction::SETTLEMENT_CONFIRMED,
        Transaction::SETTLEMENT_PENDING,
        Transaction::SUBMITTED_FOR_SETTLEMENT,
    ];

    const ACTIVE = 'Active';

    private $config;
    private $gateway;

    public function __construct()
    {
        $this->config = settings('payments.braintree');
        $this->gateway = $this->setupService();

        parent::__construct();
    }

    public function pay($user, Order $order)
    {
        try {
            $result = $this->gateway->transaction()->sale([
                'amount'             => $order->getPrice(),
                'paymentMethodNonce' => request('payment_method_nonce'),
                'options'            => [
                    'submitForSettlement' => true,
                ],
            ]);

            if ( ! $result->success || is_null($result->transaction))
                $this->handleException(new PaymentsIssueException($result->message));
        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        $this->storeSubscription($user, $order, $result->transaction->id);

        return Redirect::route('payments.pay_callback', [
            'gateway'        => $this->gatewayName(),
            'transaction_id' => $result->transaction->id,
        ]);
    }

    public function payCallback(Request $request)
    {
        try {
            $transaction = $this->gateway->transaction()->find($request->transaction_id);

            if ( ! in_array($transaction->status, self::transactionSuccessStatuses)) {
                $this->handleException(new \Exception());
            }
        } catch (\Exception $exception) {
            $this->handleException(new PaymentsUnavailableException());
        }

        $this->activateSubscription($request->transaction_id);

        return Redirect::route('payments.success');
    }

    public function subscribe($user, Order $order)
    {
        try {
            $result = $this->gateway->paymentMethod()->create([
                'customerId'         => request('customer_id'),
                'paymentMethodNonce' => request('payment_method_nonce'),
            ]);

            if ( ! $result->success || is_null($token = $result->paymentMethod->token))
                $this->handleException(new PaymentsIssueException($result->message));

            $result = $this->gateway->subscription()->create([
                'price'              => $order->getPrice(),
                'trialPeriod'        => false,
                'paymentMethodToken' => $token,
                'planId'             => $this->config['plans'][$order->plan->id],
                'merchantAccountId'  => $this->config['merchant_account_id'],
                'options'            => [
                    'startImmediately' => true,
                ],
            ]);

            if ( ! $result->success)
                $this->handleException(new PaymentsIssueException($result->message));

        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        $this->storeSubscription($user, $order, $result->subscription->id);

        return Redirect::route('payments.subscribe_callback', [
            'gateway'         => $this->gatewayName(),
            'subscription_id' => $result->subscription->id,
        ]);
    }

    public function subscribeCallback(Request $request)
    {
        try {
            $subscription = $this->gateway->subscription()->find($request->subscription_id);

            if ($subscription->status != 'Active') {
                $this->handleException(new \Exception('Subscription not active.'));
            }

            if ( ! isset($subscription->transactions[0])) {
                $this->handleException(new \Exception('Subscription does not contain transaction.'));
            }

            if ( ! in_array($subscription->transactions[0]->status, self::transactionSuccessStatuses)) {
                $this->handleException(new \Exception('Bad transaction status.'));
            }
        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        $this->activateSubscription($request->subscription_id);

        return Redirect::route('payments.success');
    }

    public function checkout(Order $order)
    {
        try {
            $result = $this->gateway->customer()->create();

            if ( ! $result->success) $this->handleException(new \Exception());

            $client_token = $this->gateway->clientToken()->generate([
                'customerId' => $result->customer->id,
            ]);
        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        if ($this->config['3d_secure'])
            return view('front::Subscriptions.Gateways.braintree_3d_secure')->with([
                'order_id'      => $order->id,
                'gateway'       => $this->gatewayName(),
                'token'         => $client_token,
                'customer_id'   => $result->customer->id,
                'email'         => $order->user->email,
                'amount'        => $order->getPrice(),
            ]);

        return view('front::Subscriptions.Gateways.braintree')->with([
            'order_id'      => $order->id,
            'gateway'       => $this->gatewayName(),
            'token'         => $client_token,
            'customer_id'   => $result->customer->id,
        ]);
    }

    public function isConfigCorrect(Request $request)
    {
        try {
            $this->buildConfig($request);

            $this->gateway = $this->setupService();

            $merchant = $this->gateway->merchantAccount()->find($this->config['merchant_account_id']);

            foreach ($this->gateway->plan()->all() as $plan) {
                if ($merchant->currencyIsoCode !== $plan->currencyIsoCode)
                    $this->handleException(
                        new \Exception(trans('admin.braintree_currencies_doesnt_match') . ' ' . $plan->id)
                    );
            }
        } catch (\Exception $e) {
            $message = $e->getMessage() ?
                ucfirst($e->getMessage()) : trans('front.login_failed');

            $this->handleException(new PaymentsConfigurationException($message));
        }

        return true;
    }

    public function getPlanIds()
    {
        $braintree_plans_ids = [];

        foreach ($this->gateway->plan()->all() as $plan) {
            $braintree_plans_ids[$plan->id] = $plan->id;
        }

        return $braintree_plans_ids;
    }

    public function isSubscriptionActive($subscription)
    {
        try {
            $subscription = $this->gateway->subscription()->find($subscription->gateway_id);
        } catch (\Exception $exception) {
            return false;
        }

        if ($subscription->status != self::ACTIVE) {
            return false;
        }

        return true;
    }

    public function isSubscriptionRenewed($subscription)
    {
        try {
            $gatewaySubscription = $this->gateway->subscription()
                ->find($subscription->gateway_id);
        } catch (\Exception $exception) {
            return false;
        }

        $gatewayDate = $gatewaySubscription->billingPeriodEndDate
            ->getTimestamp();

        if (empty($gatewayDate)) {
            return false;
        }

        $gatewayDate = Carbon::createFromTimestamp($gatewayDate);
        $subscriptionDate = Carbon::parse($subscription->expiration_date);

        $planDurationInDays = ! empty($subscription->plan)
            ? $subscription->plan->getDurationInDays()
            : null;

        return $gatewaySubscription->status == self::ACTIVE
            && $this->compareDatesByPlan($subscriptionDate, $gatewayDate, $planDurationInDays);
    }

    public function getSubscriptionEnd($subscription)
    {
        try {
            $gatewaySubscription = $this->gateway->subscription()
                ->find($subscription->gateway_id);
        } catch (\Exception $exception) {
            return null;
        }

        $date = $gatewaySubscription->billingPeriodEndDate
            ->format('Y-m-d H:i:s');

        if (empty($date)) {
            return null;
        }

        return $date;
    }

    public function storeConfig($request, $gateway)
    {
        $this->buildConfig($request);

        settings('payments.' . $gateway, array_merge(settings('payments.' . $gateway), $this->config));
        settings('payments.gateways.' . $gateway, $request->active ? 1 : 0);
    }

    private function buildConfig($request)
    {
        $config = $request->except('_token', 'active', 'plan_ids', 'billing_plans');

        try {
            $config['plans'] = array_combine($request->billing_plans, $request->plan_ids);
        } catch (\Exception $e) {
            //throw new \Exception(trans('front.attribute_missing', ['attribute' => trans('validation.attributes.default_billing_plan')]));
        }

        $this->config = $config;
    }

    private function setupService()
    {
        return new Braintree_Gateway(
            new Braintree_Configuration(Arr::only($this->config, [
                'environment',
                'merchantId',
                'publicKey',
                'privateKey',
            ]))
        );
    }

    public function cancelSubscription($subscription)
    {
        try {
            $this->gateway->subscription()->cancel($subscription->gateway_id);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }
}