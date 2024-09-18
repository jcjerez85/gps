<?php namespace Tobuli\Validation;

class SMSGatewayFormValidator extends Validator
{

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'server' => [
        ],
        'app' => [
            'user_id' => 'exists:users,id'
        ],
        'post' => [
            'sms_gateway_url' => 'required|contains:%NUMBER%|contains:%MESSAGE%',
            'username' => 'required_if:authentication,1',
            'password' => 'required_if:authentication,1',
            'custom_headers' => 'key_value_format'
        ],
        'get' => [
            'sms_gateway_url' => 'required|contains:%NUMBER%|contains:%MESSAGE%',
            'username' => 'required_if:authentication,1',
            'password' => 'required_if:authentication,1',
            'custom_headers' => 'key_value_format'
        ],
        'plivo' => [
            'auth_id' => 'required',
            'auth_token' => 'required',
            'senders_phone' => 'required',
        ],
    ];

}   //end of class

//EOF