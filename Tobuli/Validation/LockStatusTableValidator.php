<?php

namespace Tobuli\Validation;

class LockStatusTableValidator extends Validator
{
    public $rules = [
        'table' => [
            'period' => 'required|in:today,yesterday,this_week,last_week',
        ],
    ];
}
