<?php

namespace Tobuli\Helpers\Payments\Gateways;


use App\Exceptions\PaymentsConfigurationException;
use App\Exceptions\PaymentsIssueException;
use App\Exceptions\PaymentsUnavailableException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Tobuli\Entities\Subscription;
use Tobuli\Entities\Order;

class MobileDirectDebitGateway extends PaymentGateway implements PaymentGatewayInterface
{
    private $client;

    private $config;

    public function __construct()
    {
        $this->config = settings('payments.mobile_direct_debit');

        $this->client = new Client();

        parent::__construct();
    }

    public function pay($user, Order $order)
    {
        return $this->subscribe($user, $order);
    }

    public function payCallback(Request $request)
    {
        return $this->subscribeCallback($request);
    }

    public function subscribe($user, Order $order)
    {
        $referenceNo = $this->getReferenceNo();
        $phone = request('phone');

        $data = $this->makePaymentData($order->plan, $referenceNo, $phone);

        try {
            $responese = $this->call('POST', 'mobiledebit/create/mandate', $data);
        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        if (empty($responese['responseCode']))
            $this->handleException(new PaymentsUnavailableException());

        if ( ! in_array($responese['responseCode'], ['01', '03']))
            $this->handleException(new PaymentsIssueException($responese['responseMessage']));

        $this->storeSubscription($user, $order, $referenceNo);

        return Redirect::route('payments.success');
    }

    public function subscribeCallback(Request $request)
    {
        $referenceNo = $request->get('thirdPartyReferenceNo');

        $subscription = Subscription::where('gateway_id', $referenceNo)->first();

        if ( ! $subscription)
            $this->handleException(new \Exception('Subscription not found for activation!'));

        $this->activateSubscription($referenceNo);

        return Response::json([
            "responseCode" => "01",
            "responseMessage" => "Callback Successful."
        ]);
    }

    public function checkout(Order $order)
    {
        return view('front::Subscriptions.Gateways.mobile_direct_debit')->with([
            'order_id'      => $order->id,
            'gateway'       => $this->gatewayName(),
        ]);
    }

    public function isConfigCorrect(Request $request)
    {
        try {
            $responese = $this->call('POST', 'mobiledebit/create/mandate', [
                "apiKey" => $this->config['api_key'],
                "merchantId" => $this->config['merchant_id'],
                "productId" => $this->config['product_id'],
                "clientPhone" => '1111111111',
                "thirdPartyReferenceNo" => $this->getReferenceNo(),
                "amountToDebit" => "1.00",
                "frequencyType" => "Daily",
                "frequency" => "1",
            ]);
        } catch (\Exception $e) {
            $this->handleException(new PaymentsConfigurationException($e->getMessage()));
        }

        return true;
    }

    public function isSubscriptionActive($subscription)
    {
        try {
            $_subscription = $this->getSubscription($subscription);
        } catch (\Exception $e) {
            return false;
        }

        if ( ! $_subscription)
            return false;

        return true;
    }

    public function cancelSubscription($subscription)
    {
        try {
            $_subscription = $this->getSubscription($subscription);

            $this->call('POST', 'mobiledebit/cancel/mandate', [
                "merchantId" => $this->config['merchant_id'],
                "productId" => $this->config['product_id'],
                "clientPhone" => $_subscription['clientPhone'],
                //"mandateId" => $_subscription['mandateId'],
                "apiKey" => $this->config['api_key'],
            ]);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    private function getSubscription($subscription)
    {
        try {
            $response = $this->call('GET', "mobiledebit/mandate/status/{$subscription->gateway_id}");
        } catch (\Exception $exception) {
            return null;
        }

        if (empty($response['clientPhone']))
            return null;

        return $response;
    }

    private function makePaymentData($plan, $referenceNo, $phone)
    {
        switch ($plan->duration_type) {
            case 'days':
                $frequencyType = 'Daily';
                break;
            case 'months':
                $frequencyType = 'Monthly';
                break;
            case 'years':
                $frequencyType = 'Annually';
                break;
            default:
                $frequencyType = null;
        }

        return [
            "apiKey" => $this->config['api_key'],
            "merchantId" => $this->config['merchant_id'],
            "productId" => $this->config['product_id'],
            "clientPhone" => $phone,
            "thirdPartyReferenceNo" => $referenceNo,
            "amountToDebit" => $plan->price,
            "frequencyType" => $frequencyType,
            "frequency" => $plan->duration_value,
            "debitDay" => "1",
        ];
    }

    private function getReferenceNo()
    {
        return "mdd_" . Str::random(16);
    }

    private function call($method, $endpoint, $data = [])
    {
        $url = Str::finish($this->config['url'], '/');

        try {
            $response = $this->client->request($method, $url . $endpoint, [
                RequestOptions::JSON => $data
            ]);

            $result = json_decode($response->getBody(), true);

            file_put_contents(storage_path('logs/mobile_direct_debit.log'), $response->getBody() . "\n", FILE_APPEND);
        } catch (\Exception $e) {
            $this->handleException(new PaymentsUnavailableException());
        }

        return $result;
    }
}