<?php

namespace Tobuli\Validation;

class LockStatusUnlockValidator extends Validator
{
    public $rules = [
        'unlock' => [
            'id' => 'required|exists:devices,id',
            'message' => 'required|string',
            'type' => 'required|in:sms,gprs',
        ],
    ];
}
