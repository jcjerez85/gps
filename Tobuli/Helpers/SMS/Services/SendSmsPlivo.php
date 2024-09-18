<?php

namespace Tobuli\Helpers\SMS\Services;

use Plivo\RestAPI;
use Tobuli\Exceptions\ValidationException;

class SendSmsPlivo extends SendSmsManager
{
    private $senderPhone;
    private $senderId;
    private $senderToken;

    public function __construct($gateway_args)
    {
        $this->senderPhone = $gateway_args['senders_phone'];
        $this->senderId = $gateway_args['auth_id'];
        $this->senderToken = $gateway_args['auth_token'];
    }

    public function sendSingle($receiver_phone, $message_body)
    {
        $plivo_service = new RestAPI($this->senderId, $this->senderToken);

        $response = $plivo_service->send_message([
            'src' => $this->senderPhone,
            'dst' => $receiver_phone,
            'text' => $message_body,
        ]);

        if (isset($response['response']['error']))
            throw new ValidationException(['request_method' => $response['response']['error']]);

        return $response;
    }
}