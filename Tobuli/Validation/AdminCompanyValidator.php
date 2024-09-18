<?php namespace Tobuli\Validation;

use Tobuli\Entities\DevicePlan;

class AdminCompanyValidator extends Validator {
    public $rules = [
        'create' => [
            'name' => 'required|string',
        ],
        'update' => [
            'name' => 'required|string',
        ]
    ];
}
