<?php

namespace LaravelFCM\Response\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use LaravelFCM\Response\DownstreamResponse;

class ServerResponseException extends Exception
{
    /**
     * The value of the first Retry-After header in the response.
     *
     * @see https://httpwg.org/specs/rfc7231.html#header.retry-after
     * @var int|string|null
     */
    public $retryAfter;

    /**
     * ServerResponseException constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $code = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();
        $this->retryAfter = DownstreamResponse::getRetryAfterHeader($response);
        parent::__construct($responseBody, $code);
    }

    /**
     * @return int|string|null
     */
    public function getRetryAfterHeaderValue()
    {
        return $this->retryAfter;
    }
}
