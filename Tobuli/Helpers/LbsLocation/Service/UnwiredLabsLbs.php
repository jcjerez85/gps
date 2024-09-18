<?php

namespace Tobuli\Helpers\LbsLocation\Service;

use CurlResponse;
use RuntimeException;
use Tobuli\Helpers\LbsLocation\Service\Exception\AuthException;
use Tobuli\Helpers\LbsLocation\Service\Exception\RequestLimitException;

/**
 * @link https://unwiredlabs.com/api
 */
class UnwiredLabsLbs extends AbstractCustomLbs
{
    protected $serviceUrl = 'https://us1.unwiredlabs.com/v2/process.php';

    public function __construct(array $settings)
    {
        parent::__construct($settings);

        $this->client->options['CURLOPT_URL'] = $this->serviceUrl;
        $this->client->options['CURLOPT_RETURNTRANSFER'] = true;
        $this->client->options['CURLOPT_ENCODING'] = '';
        $this->client->options['CURLOPT_MAXREDIRS'] = 10;
        $this->client->options['CURLOPT_HTTP_VERSION'] = CURL_HTTP_VERSION_1_1;
        $this->client->options['CURLOPT_CUSTOMREQUEST'] = 'POST';
        $this->client->headers['Content-Type'] = 'application/json';
        $this->client->headers['charset'] = 'UTF-8';
    }

    protected function getRequestBody(array $data): array
    {
        $body = [
            'token' => $this->apiKey,
            'address' => 0,
        ];

        $body['radio'] = $data['radioType'];
        $body['ipf'] = (int)$data['considerIp'];
        $this->append($data, 'homeMobileCountryCode', $body, 'mcc');
        $this->append($data, 'homeMobileNetworkCode', $body, 'mnc');

        if (isset($data['cellTowers'])) {
            $body['cells'] = [];

            foreach ($data['cellTowers'] as $tower) {
                $cell = [];

                $this->append($tower, 'radioType', $cell, 'radio');
                $this->append($tower, 'cellId', $cell, 'cid');
                $this->append($tower, 'locationAreaCode', $cell, 'lac');
                $this->append($tower, 'mobileCountryCode', $cell, 'mcc');
                $this->append($tower, 'mobileNetworkCode', $cell, 'mnc');
                $this->append($tower, 'signalStrength', $cell, 'signal');

                $body['cells'][] = $cell;
            }
        }

        if (isset($data['wifiAccessPoints'])) {
            $body['wifi'] = [];

            foreach ($data['wifiAccessPoints'] as $wap) {
                $wifi = [];

                $this->append($wap, 'macAddress', $wifi, 'bssid');
                $this->append($wap, 'signalStrength', $wifi, 'signal');
                $this->append($wap, 'channel', $wifi, 'channel');

                $body['wifi'][] = $wifi;
            }
        }

        return $body;
    }

    protected function request(array $data): array
    {
        $response = $this->client->post($this->serviceUrl, json_encode($data));

        return $this->validateResponse($response);
    }

    protected function validateResponse(CurlResponse $response): array
    {
        $body = parent::validateResponse($response);
        $msg = $body['message'] ?? '';

        if (strpos($msg, 'Invalid token') !== false) {
            throw new AuthException($response->body);
        }

        if (strpos($msg, 'Token balance over') !== false) {
            throw new RequestLimitException($response->body);
        }

        return $body;
    }

    protected function formatResponse(array $data): array
    {
        if (!isset($data['lat']) || !isset($data['lon'])) {
            throw new RuntimeException(json_encode($data));
        }

        return [
            'lat' => $data['lat'],
            'lng' => $data['lon'],
            'accuracy' => $data['accuracy'] ?? null,
        ];
    }
}