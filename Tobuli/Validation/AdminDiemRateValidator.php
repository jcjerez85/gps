<?php

namespace Tobuli\Validation;

class AdminDiemRateValidator extends Validator
{
    public $rules = [
        'save' => [
            'title'             => 'required|unique:diem_rates,title,%s',
            'active'            => 'required|boolean',
            'rates'             => 'required|array',
            'rates.*'           => 'array',
            'rates.*.amount'    => 'required|numeric',
            'rates.*.period'    => 'required|integer|min:1|max:24',
        ],
    ];
}
