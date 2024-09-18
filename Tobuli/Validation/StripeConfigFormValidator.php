<?php

namespace Tobuli\Validation;

class StripeConfigFormValidator extends Validator
{
    public $rules = [
        'update' => [
            'public_key' => 'required',
            'secret_key' => 'required',
            'currency'   => 'required',
        ],
    ];
}