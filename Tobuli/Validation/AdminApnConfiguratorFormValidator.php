<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class AdminApnConfiguratorFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            'apn_name' => 'required',
        ],
        'update' => [
            'id' => 'required|exists:apn_config,id',
            'name' => 'required',
            'apn_name' => 'required',
        ],
    ];
}
