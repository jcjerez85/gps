<?php namespace Tobuli\Validation;

class AdminLanguageFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'title' => 'required',
        ]
    ];

}   //end of class


//EOF