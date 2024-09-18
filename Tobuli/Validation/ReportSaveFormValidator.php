<?php namespace Tobuli\Validation;

class ReportSaveFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'devices' => 'required_unless:type,35,36,38|array',
            'title' => 'required',
            'speed_limit' => 'numeric',
            'geofences' => 'array'
        ]
    ];

}   //end of class


//EOF