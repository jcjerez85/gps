<?php namespace Tobuli\Validation;

class CompanyValidator extends Validator
{
    public $rules = [
        'write' => [
            'name' => 'required|string',
        ]
    ];
}
