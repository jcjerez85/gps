<?php

namespace Tobuli\Validation;

class MobileDirectDebitConfigFormValidator extends Validator
{
    public $rules = [
        'update' => [
            'api_key'          => 'required',
            'url'              => 'required',
            'product_id'       => 'required',
            'merchant_id'      => 'required',
        ],
    ];
}