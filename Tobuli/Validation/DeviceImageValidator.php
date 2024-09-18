<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class DeviceImageValidator extends Validator
{

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'upload' => [
            'image' => 'required|image_valid|image|max:2048',
        ],
    ];
}
