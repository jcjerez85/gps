<?php

namespace Tobuli\Validation;

class PaypalConfigFormValidator extends Validator
{
    public $rules = [
        'update' => [
            'client_id'     => 'required',
            'secret'        => 'required',
            'currency'      => 'required',
            'payment_name'  => 'required',
        ]
    ];
}