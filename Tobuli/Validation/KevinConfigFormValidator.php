<?php

namespace Tobuli\Validation;

class KevinConfigFormValidator extends Validator
{
    public $rules = [
        'update' => [
            'client_id'         => 'required',
            'client_secret'     => 'required',
            'endpoint_secret'   => 'required',
            'currency'          => 'required',
            'language'          => 'required',
            'receiver_name'     => 'required',
            'receiver_iban'     => 'required',
        ],
    ];
}