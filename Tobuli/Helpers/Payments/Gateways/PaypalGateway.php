<?php

namespace Tobuli\Helpers\Payments\Gateways;

use App\Exceptions\PaymentsConfigurationException;
use App\Exceptions\PaymentsUnavailableException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\Agreement;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\AgreementStateDescriptor;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\Amount;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\Currency;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\MerchantPreferences;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\Patch;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\PatchRequest;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\Payer;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\Payment;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\PaymentDefinition;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\PaymentExecution;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\Plan;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\RedirectUrls;
use Tobuli\Helpers\Payments\Gateways\PayPal\Api\Transaction;
use Tobuli\Helpers\Payments\Gateways\PayPal\Auth\OAuthTokenCredential;
use Tobuli\Helpers\Payments\Gateways\PayPal\Common\PayPalModel;
use Tobuli\Helpers\Payments\Gateways\PayPal\Exception\PayPalConnectionException;
use Tobuli\Helpers\Payments\Gateways\PayPal\Rest\ApiContext;
use Tobuli\Entities\Order;

class PaypalGateway extends PaymentGateway implements PaymentGatewayInterface
{
    const ACTIVE = 'Active';

    private $config;
    private $_apiContext;
    private $currency;

    public function __construct()
    {
        $this->config = settings('payments.paypal');
        $this->config['settings'] = config('payments.paypal.settings');
        $this->config['settings']['mode'] = $this->config['mode'];
        $this->currency = strtoupper($this->config['currency']);
        $this->setup();

        parent::__construct();
    }

    public function pay($user, Order $order)
    {
        try {
            $payment = $this->createPayment($order->plan->price);

            $url = $payment->getApprovalLink();

            if ( ! $url)
                $this->handleException(new \Exception(), 'No approval link');
        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        $this->storeSubscription($user, $order, $payment->getId());

        return Redirect::away($url);
    }

    public function payCallback(Request $request)
    {
        $payment = Payment::get($request->paymentId, $this->_apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->PayerID);

        try {
            $payment->execute($execution, $this->_apiContext);
            $payment = Payment::get($request->paymentId, $this->_apiContext);
        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException(), [$request->paymentId]);
        }

        $this->activateSubscription($payment->getId());

        return Redirect::route('payments.success');
    }

    public function subscribe($user, Order $order)
    {
        try {
            $billing_plan = $this->createBillingPlan($order);
            $billing_plan = $this->activatePlan($billing_plan);

            $agreement = $this->createAgreement($billing_plan->getId());

            $url = $agreement->getApprovalLink();

            if ( ! $url)
                $this->handleException(new \Exception(), 'No approval link');

        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        // Store token, because there is no agreement id here and there is no billing plan id in callback.
        // https://github.com/paypal/PayPal-REST-API-issues/issues/92 | Paypal bug!
        $parts = parse_url($url);
        parse_str($parts['query'], $query);

        $this->storeSubscription($user, $order, $query['token']);

        return Redirect::away($url);
    }

    public function subscribeCallback(Request $request)
    {
        $agreement = new Agreement();

        try {
            $agreement->execute($request->token, $this->_apiContext);

            $agreement = Agreement::get($agreement->getId(), $this->_apiContext);

            if ($agreement->getState() != self::ACTIVE)
                throw new PaymentsUnavailableException();

        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        $this->activateSubscription($request->token, ['gateway_id' => $agreement->getId()]);

        return Redirect::route('payments.success');
    }

    public function checkout(Order $order)
    {
        return Redirect::route('payments.subscribe', [
            'order_id'      => $order->id,
            'gateway'       => $this->gatewayName(),
        ]);
    }

    public function isConfigCorrect(Request $request)
    {
        try {
            $this->config = array_replace($this->config, $request->except('mode', '_token'));
            $this->config['settings']['mode'] = $request->mode;
            $this->setup();
            $this->createPayment(1);
        } catch (PayPalConnectionException $e) {
            $response = json_decode($e->getData());

            if ($response) {
                $message = $response->details[0]->issue ?? $response->error_description;
            } else {
                $message = $e->getMessage();
            }

            $this->handleException(new PaymentsConfigurationException($message), $response);
        }

        return true;
    }

    public function isSubscriptionActive($subscription)
    {
        try {
            $agreement = Agreement::get($subscription->gateway_id, $this->_apiContext);
        } catch (\Exception $exception) {
            return false;
        }

        if ($agreement->getState() != self::ACTIVE)
            return false;

        return true;
    }

    public function isSubscriptionRenewed($subscription)
    {
        try {
            $agreement = Agreement::get($subscription->gateway_id, $this->_apiContext);
        } catch (\Exception $exception) {
            return false;
        }

        $gatewayDate = $agreement->agreement_details
            ->getNextBillingDate();

        if (empty($gatewayDate)) {
            return false;
        }

        $gatewayDate = Carbon::parse($gatewayDate);
        $subscriptionDate = Carbon::parse($subscription->expiration_date);
        $planDurationInDays = ! empty($subscription->plan)
            ? $subscription->plan->getDurationInDays()
            : null;

        return $agreement->getState() == self::ACTIVE
            && $this->compareDatesByPlan($subscriptionDate, $gatewayDate, $planDurationInDays);
    }

    public function getSubscriptionEnd($subscription)
    {
        try {
            $agreement = Agreement::get($subscription->gateway_id, $this->_apiContext);
        } catch (\Exception $exception) {
            return null;
        }

        $date = $agreement->agreement_details
            ->getNextBillingDate();

        if (empty($date)) {
            return null;
        }

        return $date;
    }

    private function setup()
    {
        $this->_apiContext = new ApiContext(
            new OAuthTokenCredential($this->config['client_id'], $this->config['secret'])
        );
        $this->_apiContext->setConfig($this->config['settings']);
    }

    private function createPayment($price)
    {
        $amount = new Amount();
        $amount->setTotal($price)
            ->setCurrency($this->currency);

        $transaction = new Transaction();
        $transaction->setAmount($amount);

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('payments.pay_callback', ['gateway' => $this->gatewayName()]))
                      ->setCancelUrl(route('payments.cancel'));

        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer((new Payer())
                ->setPaymentMethod($this->gatewayName()))
            ->setRedirectUrls($redirect_urls)
            ->setTransactions([$transaction]);

        return $payment->create($this->_apiContext);
    }

    private function createBillingPlan(Order $order)
    {
        $billing_plan = new Plan();
        $billing_plan->setName($this->config['payment_name'])
            ->setDescription($this->config['payment_name'])
            ->setType('INFINITE'); // FIXED or INFINITE

        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName($this->config['payment_name'])
            ->setType('REGULAR')// TRIAL or REGULAR
            ->setFrequency(strtoupper(substr_replace($order->plan->duration_type, '', -1)))
            ->setFrequencyInterval((string)$order->plan->duration_value)
            ->setCycles('0')
            ->setAmount(new Currency(['value' => $order->getPrice(), 'currency' => $this->currency]));

        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl(route('payments.subscribe_callback', ['gateway' => $this->gatewayName()]))
            ->setCancelUrl(route('payments.cancel'))
            ->setAutoBillAmount('YES')
            ->setInitialFailAmountAction('CANCEL')
            ->setMaxFailAttempts('2');

        $billing_plan->setPaymentDefinitions([$paymentDefinition]);
        $billing_plan->setMerchantPreferences($merchantPreferences);

        return $billing_plan->create($this->_apiContext);
    }

    private function activatePlan(Plan $billing_plan)
    {
        $patch = new Patch();
        $patch->setOp('replace')
            ->setPath('/')
            ->setValue(new PayPalModel('{"state":"ACTIVE"}'));

        $patchRequest = new PatchRequest();
        $patchRequest->addPatch($patch);

        $billing_plan->update($patchRequest, $this->_apiContext);

        return Plan::get($billing_plan->getId(), $this->_apiContext);
    }

    private function createAgreement($plan_id)
    {
        $agreement = new Agreement();
        $agreement->setName($this->config['payment_name'])
            ->setDescription($this->config['payment_name'])
            ->setPayer((new Payer())
                ->setPaymentMethod($this->gatewayName()))
            ->setPlan((new Plan())
                ->setId($plan_id))
            ->setStartDate(date('Y-m-d', strtotime('1 day')) . 'T' . date('H:i:s') . 'Z');
        // The start date must be no less than 24 hours after the current date
        // as the agreement can take up to 24 hours to activate.

        return $agreement->create($this->_apiContext);
    }

    public function cancelSubscription($subscription)
    {
        $state_descriptor = new AgreementStateDescriptor();
        $state_descriptor->setNote('Cancel due to end of service.');

        try {
            $agreement = Agreement::get($subscription->gateway_id, $this->_apiContext);
            $agreement->cancel($state_descriptor, $this->_apiContext);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }
}