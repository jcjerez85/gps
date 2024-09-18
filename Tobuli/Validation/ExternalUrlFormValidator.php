<?php

namespace Tobuli\Validation;

class ExternalUrlFormValidator extends Validator
{

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'enabled' => 'required|boolean',
            'external_url' => 'required|url',
        ],
    ];

}
