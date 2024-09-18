<?php

namespace Tobuli\Helpers\LbsLocation\Service;

use Curl;
use CurlResponse;
use RuntimeException;

abstract class AbstractLbs implements LbsInterface
{
    /**
     * @var Curl
     */
    protected $client;

    protected $apiKey;

    protected $serviceUrl;

    public function __construct(array $settings)
    {
        $this->apiKey = $settings['api_key'];
        $this->client = new Curl();
        $this->client->options['CURLOPT_TIMEOUT'] = 3;
        $this->client->options['CURLOPT_CONNECTTIMEOUT'] = 3;
    }

    public function getLocation(array $data): array
    {
        $data = $this->getRequestBody($data);

        $response = $this->request($data);

        return $this->formatResponse($response);
    }

    /**
     * @link https://github.com/traccar/traccar/blob/master/src/main/java/org/traccar/model/Network.java
     * For max speed everything adjusted according to traccar data models
     */
    abstract protected function getRequestBody(array $data): array;

    abstract protected function request(array $data): array;


    protected function validateResponse(CurlResponse $response): array
    {
        if (!is_array($body = json_decode($response->body, true))) {
            throw new RuntimeException($response);
        }

        return $body;
    }

    abstract protected function formatResponse(array $data): array;
}