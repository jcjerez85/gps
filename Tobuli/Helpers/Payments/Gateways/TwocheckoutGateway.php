<?php

namespace Tobuli\Helpers\Payments\Gateways;

use App\Exceptions\PaymentsConfigurationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use Tobuli\Entities\Order;
use Tobuli\Entities\Subscription;
use Tobuli\Helpers\Payments\Gateways\Twocheckout\TwocheckoutClient;
use Tobuli\Helpers\Payments\Gateways\Twocheckout\TwocheckoutConfig;
use Tobuli\Helpers\Payments\Gateways\Twocheckout\TwocheckoutSecurity;

class TwocheckoutGateway extends PaymentGateway implements PaymentGatewayInterface
{
    private $client;

    public function __construct()
    {
        $this->client = new TwocheckoutClient();
    }

    public function pay($user, Order $order): RedirectResponse
    {
        $merchantOrderId = uniqid(null, true);

        $this->storeSubscription($user, $order, $merchantOrderId);

        return $this->buildCheckoutRedirect($order, $merchantOrderId, false);
    }

    public function subscribe($user, Order $order): RedirectResponse
    {
        $merchantOrderId = uniqid(null, true);

        $this->storeSubscription($user, $order, $merchantOrderId);

        return $this->buildCheckoutRedirect($order, $merchantOrderId, true);
    }

    private function buildCheckoutRedirect(Order $order, $merchantOrderId, bool $recurringPayment): RedirectResponse
    {
        $fields = [
            'sid' => TwocheckoutConfig::getMerchantCode(),
            'mode' => '2CO',
            'merchant_order_id' => $merchantOrderId,
            'li_0_name' => $order->plan->title,
            'li_0_price' => $order->getPrice(),
        ];

        if (TwocheckoutConfig::isDemoMode()) {
            $fields['demo'] = 'Y';
        }

        if ($recurringPayment && isset($order->plan)) {
            $fields['li_0_recurrence'] = $this->calculateRecurrence($order->plan);
        }

        return Redirect::away(TwocheckoutConfig::getFrontUrl() . '/checkout/purchase?' . http_build_query($fields));
    }

    private function calculateRecurrence($plan)
    {
        $type = $plan->duration_type ?: '';
        $value = $plan->duration_value ?: -1;

        switch (true) {
            case $type === 'months':
                return $value . ' Month';
            case $type === 'years':
                return ($value * 12) . ' Month';
            case $type === 'days' && $value % 7 === 0:
                return ($value / 7) . ' Week';
            case $type === 'days' && $value % 30 === 0:
                return ($value / 30) . ' Month';
            default:
                return null;
        }
    }

    public function checkout(Order $order): RedirectResponse
    {
        return Redirect::route('payments.subscribe', [
            'plan_id'       => $order->id,
            'gateway'       => $this->gatewayName(),
        ]);
    }

    public function webhook(Request $request): Response
    {
        $input = $request->input();

        if ((new TwocheckoutSecurity())->hasValidHash($input) === false) {
            return new Response('Invalid hash'); // Do not change status code, otherwise URL cannot be set for IPN
        }

        $order = $this->client->request('GET', '/orders/' . $input['REFNO']);

        if (($order->Status ?? '') !== 'COMPLETE') {
            return new Response('Order #' . $input['REFNO'] . ' is not finished', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($this->processSubscriptionWebhook($order)) {
            return new Response('Subscription renewed');
        }

        if (!empty($input['REFNOEXT'] && $this->processPaymentWebhook($order, $input['REFNOEXT']))) {
            return new Response('Payment processed');
        }

        return new Response('Omitted');
    }

    private function processSubscriptionWebhook($order): bool
    {
        $subRefNo = $order->Items[0]->ProductDetails->Subscriptions[0]->SubscriptionReference ?? null;

        if (!empty($subRefNo) && $subscription = Subscription::where('gateway_id', $subRefNo)->first()) {
            $this->renewSubscription($subscription);

            return true;
        }

        return false;
    }

    private function processPaymentWebhook($order, $externalRefNo): bool
    {
        $subscription = Subscription::where('gateway_id', $externalRefNo)->first();

        if (!$subscription) {
            return false;
        }

        $this->activateSubscription($externalRefNo);

        $subRefNo = $order->Items[0]->ProductDetails->Subscriptions[0]->SubscriptionReference;
        $recurringEnabled = $order->Items[0]->ProductDetails->Subscriptions[0]->RecurringEnabled;

        if ($recurringEnabled) {
            $subscription->update(['gateway_id' => $subRefNo]);
        }

        return true;
    }

    public function payCallback(Request $request): Response
    {
        return new Response('Payments are processed using IPN.');
    }

    public function subscribeCallback(Request $request): Response
    {
        return new Response('Payments are processed using IPN.');
    }

    public function isConfigCorrect(Request $request): bool
    {
        // API does not have "ping" endpoint, checking some vendor info instead
        $response = $this->client->request('GET', '/countries/');

        if (!$response || isset($response->error_code)) {
            throw new PaymentsConfigurationException(
                sprintf('%s: %s', $response->error_code ?? 'No response', $response->message ?? '')
            );
        }

        return true;
    }

    public function isSubscriptionActive($subscription): bool
    {
        $subInfo = $this->client->request('GET', '/subscriptions/' . $subscription->gateway_id);

        return ($subInfo->RecurringEnabled ?? false) && (($subInfo->Status ?? '') === 'ACTIVE');
    }

    public function cancelSubscription($subscription): bool
    {
        return $this->client->request('DELETE', '/subscriptions/' . $subscription->gateway_id) === true;
    }
}
