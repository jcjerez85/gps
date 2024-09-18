<?php

namespace Tobuli\Helpers\Payments\Gateways;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use InvalidArgumentException;
use Kevin\Client;
use Kevin\KevinException;
use Kevin\SecurityManager;
use Symfony\Component\HttpFoundation\Response;
use Tobuli\Entities\Order;

class KevinGateway extends PaymentGateway implements PaymentGatewayInterface
{
    const CLIENT_OPTIONS = [
        'error'     => 'exception',
        'version'   => '0.3',
    ];
    const WEBHOOK_TIMEOUT_MS = 300000;

    private $client;
    private $config;

    public function __construct()
    {
        $config = settings('payments.kevin');

        $this->config = $config;
        $this->client = new Client(
            $config['client_id'] ?: '-',
            $config['client_secret'] ?: '-',
            ['lang' => $config['language']] + self::CLIENT_OPTIONS
        );

        parent::__construct();
    }

    public function pay($user, Order $order): RedirectResponse
    {
        $attributes = [
            'Redirect-URL' => route('payments.success'),
            'Webhook-URL' => route('payments.webhook', ['gateway' => $this->gatewayName()]),
            'description' => $order->plan->title,
            'currencyCode' => $this->config['currency'],
            'amount' => $order->getPrice(),
            'bankPaymentMethod' => [
                'endToEndId' => (string)$order->id,
                'creditorName' => $this->config['receiver_name'], // strongly encouraged to use only latin characters
                'creditorAccount' => [
                    'iban' => $this->config['receiver_iban'], // must be the one from the agreement with kevin.
                ],
            ],
            'cardPaymentMethod' => [],
        ];

        try {
            $response = $this->client->payment()->initPayment($attributes);
        } catch (Exception $e) {
            $this->handleException($e);
        }

        if (!isset($response['id']) || !isset($response['confirmLink'])) {
            $this->handleException(new InvalidArgumentException(), $response);
        }

        $this->storeSubscription($user, $order, $response['id']);

        return Redirect::away($response['confirmLink']);
    }

    public function subscribe($user, Order $order): RedirectResponse
    {
        return $this->pay($user, $order);
    }

    public function payCallback(Request $request): Response
    {
        return new Response('Payments are processed by webhook.');
    }

    public function subscribeCallback(Request $request): Response
    {
        return $this->payCallback($request);
    }

    public function checkout(Order $order)
    {
        return Redirect::route('payments.subscribe', [
            'plan_id'       => $order->id,
            'gateway'       => $this->gatewayName(),
        ]);
    }

    public function webhook(Request $request): Response
    {
        $requestBody = $request->getContent();
        $headers = $request->headers->all();

        array_walk($headers, function (&$item) {
            $item = $item[0];
        });

        $isValid = SecurityManager::verifySignature(
            $this->config['endpoint_secret'],
            $requestBody,
            $headers,
            route('payments.webhook', ['gateway' => $this->gatewayName()]),
            self::WEBHOOK_TIMEOUT_MS
        );

        if (!$isValid) {
            return new Response('', 400);
        }

        $body = json_decode($requestBody, true);

        if (!isset($body['statusGroup']) || !isset($body['id'])) {
            $this->handleException(new InvalidArgumentException('Invalid response: ' . $requestBody));
        }

        if ($body['statusGroup'] !== 'completed') {
            $this->handleException(new InvalidArgumentException('Invalid statusGroup: ' . $requestBody));
        }

        $this->activateSubscription($body['id']);

        return new Response();
    }

    public function isConfigCorrect(Request $request): bool
    {
        try {
            $client = new Client($request->get('client_id'), $request->get('client_secret'));
            $client->auth()->getProjectSettings();
        } catch (KevinException $e) {
            $this->handleException($e);
        }

        return true;
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
