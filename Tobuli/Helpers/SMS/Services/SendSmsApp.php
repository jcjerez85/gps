<?php

namespace Tobuli\Helpers\SMS\Services;

use CustomFacades\Repositories\SmsEventQueueRepo;

class SendSmsApp extends SendSmsManager
{
    private $user_id;

    /**
     * SendSmsApp constructor.
     * @param $gateway_args
     */
    public function __construct($gateway_args)
    {
        $this->user_id = $gateway_args['user_id'];
    }

    /**
     * @param $receiver_phone
     * @param $message_body
     */
    protected function sendSingle($receiver_phone, $message_body)
    {
        SmsEventQueueRepo::create([
            'user_id' => $this->user_id,
            'phone'   => $receiver_phone,
            'message' => $message_body
        ]);
    }
}