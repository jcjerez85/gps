<?php

namespace Tobuli\Validation;

class BraintreeConfigFormValidator extends Validator
{
    public $rules = [
        'update' => [
            'merchantId'    => 'required',
            'publicKey'     => 'required',
            'privateKey'    => 'required',
            'billing_plans' => 'required',
        ],
    ];
}