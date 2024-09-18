<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class DeviceGroupFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'title'   => 'required',
            'devices' => 'nullable|array|exists:devices,id',
        ],
        'update' => [
            'title'   => 'required',
            'devices' => 'nullable|array|exists:devices,id'
        ]
    ];

}   //end of class


//EOF