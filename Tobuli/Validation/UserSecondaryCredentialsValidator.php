<?php

namespace Tobuli\Validation;

class UserSecondaryCredentialsValidator extends Validator
{
    public array $rules = [
        'create' => [
            'email' => 'required|email|unique:user_secondary_credentials,email|unique_table_msg:users,email',
            'password' => 'required|confirmed',
            'user_id' => 'required',
        ],
        'update' => [
            'email' => 'sometimes|required|email|unique:user_secondary_credentials,email,%s|unique_table_msg:users,email',
            'password' => 'confirmed',
            'user_id' => 'sometimes|required',
        ]
    ];
}
