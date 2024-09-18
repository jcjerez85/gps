<?php

namespace Tobuli\Helpers\Payments\Gateways\Twocheckout;

use Curl;

class TwocheckoutClient
{
    const HEADER_AUTH = 'X-Avangate-Authentication';

    private $curl;

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function request(string $method, string $endpoint, array $args = [])
    {
        $this->curl->headers[self::HEADER_AUTH] = $this->getAuthValue();

        return \json_decode($this->curl->request($method, TwocheckoutConfig::getApiUrl() . $endpoint, $args));
    }

    private function getAuthValue(): string
    {
        $date = date('Y-m-d H:i:s');
        $merchantCode = TwocheckoutConfig::getMerchantCode();

        $hash = hash_hmac(
            'md5',
            strlen($merchantCode) . $merchantCode . strlen($date) . $date,
            TwocheckoutConfig::getSecretKey()
        );

        return "code=\"$merchantCode\" date=\"$date\" hash=\"$hash\"";
    }
}
