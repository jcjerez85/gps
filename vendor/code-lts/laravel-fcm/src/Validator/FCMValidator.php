<?php

namespace LaravelFCM\Validator;

use LaravelFCM\Request\ValidateRequest;
use LaravelFCM\Sender\HTTPSender;
use Exception;
use Psr\Http\Message\ResponseInterface;

class FCMValidator extends HTTPSender
{

    /** @var string */
    private $validate_token_url = 'https://iid.googleapis.com/iid/info/'; // + YOUR_APP_TOKEN_HERE

    /**
     * @see https://developers.google.com/instance-id/reference/server
     *
     * @param string $token The token to validate
     * @param string|null $serverKey (optional) The server key
     * @param string|null $senderId (optional) The sender Id
     *
     * @return bool
     */
    public function validateToken(string $token, string $serverKey = null, string $senderId = null)
    {
        $request = new ValidateRequest($serverKey, $senderId);
        try {
            $build = $request->build();
            if (isset($build['json'])) {
                unset($build['json']);
            }
            return $this->isValidResponse($this->client->request('get', $this->validate_token_url . $token, $build));
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    public function isValidResponse(ResponseInterface $response)
    {
        return $response->getStatusCode() === 200;
    }
}
