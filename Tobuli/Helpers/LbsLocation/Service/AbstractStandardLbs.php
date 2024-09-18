<?php

namespace Tobuli\Helpers\LbsLocation\Service;

use CurlResponse;
use RuntimeException;
use Tobuli\Helpers\LbsLocation\Service\Exception\AuthException;
use Tobuli\Helpers\LbsLocation\Service\Exception\RequestLimitException;

abstract class AbstractStandardLbs extends AbstractLbs
{
    protected $errorPhraseKeyInvalid = 'keyInvalid';

    protected function getRequestBody(array $data): array
    {
        return $data;
    }

    protected function request(array $data): array
    {
        $response = $this->client->post($this->serviceUrl . '?key=' . $this->apiKey, json_encode($data));

        return $this->validateResponse($response);
    }

    protected function validateResponse(CurlResponse $response): array
    {
        $statusCode = $response->headers['Status-Code'];

        if ($statusCode == 403) {
            throw new RequestLimitException($response->body);
        }

        if ($statusCode == 400 && strpos($response->body, $this->errorPhraseKeyInvalid) !== false) {
            throw new AuthException($response->body);
        }

        return parent::validateResponse($response);
    }

    protected function formatResponse(array $data): array
    {
        if (!isset($data['location']['lat']) || !isset($data['location']['lng'])) {
            throw new RuntimeException(json_encode($data));
        }

        return [
            'lat' => $data['location']['lat'],
            'lng' => $data['location']['lng'],
            'accuracy' => $data['accuracy'] ?? null,
        ];
    }
}