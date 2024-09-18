<?php namespace Tobuli\Validation;

class UserAccountFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'email' => 'required|email|unique:users,email,%s|unique_table_msg:user_secondary_credentials,email',
            'password' => 'confirmed'
        ],
        'password' => [
            'password' => 'required|confirmed'
        ],
    ];
}   //end of class


//EOF