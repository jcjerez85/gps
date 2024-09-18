<?php

namespace Tobuli\Helpers\Payments\Gateways\Paydunya;

class Checkout extends Paydunya
{
    public $status;
    public $response_code;
    public $response_text;
    public $transaction_id;
    public $description;
    public $token;
}