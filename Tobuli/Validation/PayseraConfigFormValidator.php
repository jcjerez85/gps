<?php

namespace Tobuli\Validation;

class PayseraConfigFormValidator extends Validator
{
    public $rules = [
        'update' => [
            'project_id'    => 'required',
            'project_psw'   => 'required',
            'verify_id'     => 'required',
            'currency'      => 'required',
        ],
    ];
}