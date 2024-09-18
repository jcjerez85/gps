<?php

namespace Tobuli\Helpers\Payments\Gateways;

use App\Exceptions\PaymentsConfigurationException;
use App\Exceptions\PaymentsIssueException;
use App\Jobs\CreateFile;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Entities\Order;
use Tobuli\Entities\Subscription;
use WebToPay;

class PayseraGateway extends PaymentGateway implements PaymentGatewayInterface
{
    private $config;

    public function __construct()
    {
        $this->config = settings('payments.paysera');

        parent::__construct();
    }

    public function pay($user, Order $order)
    {
        $orderId = $order->id;

        $data = [
            'projectid' => $this->config['project_id'],
            'sign_password' => $this->config['project_psw'],
            'orderid' => $orderId,
            'amount' => $order->getPrice() * 100,
            'paytext' => $order->plan->title,
            'currency' => $this->config['currency'],
            'p_email' => $order->user->email,
            'accepturl' => route('payments.pay_callback', ['gateway' => $this->gatewayName()]),
            'cancelurl' => route('payments.cancel'),
            'callbackurl' => route('payments.webhook', ['gateway' => $this->gatewayName()]),
            'test' => $this->config['environment'] === 'production' ? 0 : 1,
        ];

        $this->storeSubscription($user, $order, $orderId);

        try {
            WebToPay::redirectToPayment($data, true);
        } catch (\WebToPayException $e) {
            $this->handleException($e);
        }
    }

    public function payCallback(Request $request)
    {
        $this->confirmPayment($request);

        return Redirect::route('payments.success');
    }

    public function webhook(Request $request)
    {
        $this->confirmPayment($request);

        return response('OK');
    }

    private function confirmPayment(Request $request)
    {
        $input = $request->input();

        try {
            $response = WebToPay::validateAndParseData(
                $input,
                $this->config['project_id'],
                $this->config['project_psw']
            );

            if ($response['status'] !== '1' && $response['status'] !== '3') {
                throw new Exception('Payment was not successful');
            }

            $gatewayId = $response['orderid'];

            $subscription = Subscription::where('gateway_id', $gatewayId)->first();

            /** @var Order $order */
            if ($subscription === null || ($order = $subscription->order) === null) {
                throw new PaymentsIssueException('Cannot resolve order');
            }

            if ($order->isPaid()) {
                return;
            }

            $prefix = array_key_exists('payamount', $response) ? 'pay' : '';

            if ($order->getPrice() > round($response[$prefix . 'amount'] / 100, 2)
                || $this->config['currency'] !== $response[$prefix . 'currency']
            ) {
                throw new PaymentsIssueException('Wrong payment amount');
            }
        } catch (Exception $e) {
            $this->handleException($e, json_encode($response ?? null));
        }

        $this->activateSubscription($gatewayId);
    }

    public function storeConfig($request, $gateway)
    {
        parent::storeConfig($request, $gateway);

        $verifyId = $request->get('verify_id');

        $path = public_path('paysera_' . $verifyId . '.html');

        dispatch(new CreateFile($path, $verifyId));
    }

    public function checkout(Order $order)
    {
        return Redirect::route('payments.pay', [
            'order_id' => $order->id,
            'gateway'  => $this->gatewayName(),
        ]);
    }

    public function isConfigCorrect(Request $request): bool
    {
        $data = [
            'projectid' => $request->get('project_id'),
            'sign_password' => $request->get('project_psw'),
            'orderid' => 0,
            'amount' => 1,
            'paytext' => 'Config test',
            'currency' => $request->get('currency'),
            'accepturl' => route('payments.pay_callback', ['gateway' => $this->gatewayName()]),
            'cancelurl' => route('payments.cancel'),
            'callbackurl' => route('payments.webhook', ['gateway' => $this->gatewayName()]),
            'test' => $request->get('environment') === 'production' ? 0 : 1,
        ];

        $requestData = WebToPay::buildRequest($data);

        $client = new Client();

        $client->get(WebToPay::PAY_URL, [
            'query'   => $requestData,
            'on_stats' => function (TransferStats $stats) use (&$url) {
                $url = $stats->getEffectiveUri();
            }
        ])->getBody()->getContents();


        if (strpos($url->getPath(), 'error_code') === false) {
            return true;
        }

        throw new PaymentsConfigurationException('Error');
    }

    public function subscribe($user, Order $order)
    {
        $this->pay($user, $order);
    }

    public function subscribeCallback(Request $request)
    {
        return $this->payCallback($request);
    }

    public function isSubscriptionRenewed($subscription): bool
    {
        return false;
    }

    public function isSubscriptionActive($subscription): bool
    {
        $expirationDate = $this->getSubscriptionEnd($subscription);

        return $expirationDate && date('Y-m-d') <= $expirationDate;
    }

    public function cancelSubscription($subscription): bool
    {
        return true;
    }
}