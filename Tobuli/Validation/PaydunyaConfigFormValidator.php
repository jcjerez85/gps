<?php

namespace Tobuli\Validation;

class PaydunyaConfigFormValidator extends Validator
{
    public $rules = [
        'update' => [
            'master_key'    => 'required',
            'public_key'    => 'required',
            'private_key'   => 'required',
            'token'         => 'required',
            'payment_name'  => 'required'
        ],
    ];
}