<?php

namespace Tobuli\Validation;

class TwocheckoutConfigFormValidator extends Validator
{
    public $rules = [
        'update' => [
            'api_url' => 'required',
            'front_url' => 'required',
            'merchant_code' => 'required',
            'secret_key' => 'required',
        ],
    ];
}