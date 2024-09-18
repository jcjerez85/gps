<?php namespace Tobuli\Validation;

class AdminEmailSettingsFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'use_smtp_server_0' => [
            'from_name' => 'required',
            'noreply_email' => 'required|email',
        ],
        'use_smtp_server_1' => [
            'from_name' => 'required',
            'noreply_email' => 'required|email',
            'smtp_server_host' => 'required',
            'smtp_server_port' => 'required|numeric',
            'smtp_username' => 'required_if:smtp_authentication,1',
            'smtp_password' => 'required_if:smtp_authentication,1'
        ],
        'sendgrid' => [
            'from_name' => 'required',
            'noreply_email' => 'required|email',
            'api_key' => 'required',
        ],
        'mailgun' => [
            'from_name' => 'required',
            'noreply_email' => 'required|email',
            'api_key' => 'required',
            'domain' => 'required',
            'region' => 'required|in:0,eu',
        ],
    ];

}   //end of class


//EOF