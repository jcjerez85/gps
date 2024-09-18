<?php namespace Tobuli\Validation;

use Tobuli\Entities\DevicePlan;

class AdminDeviceTypeValidator extends Validator {
    public $rules = [
        'create' => [
            'title' => 'required|string',
            'active' => 'required|boolean',
            'image' => 'required|image|max:2048',
            'sensor_group_id' => 'integer'
        ],
        'update' => [
            'title' => 'required|string',
            'active' => 'required|boolean',
            'image' => 'image|max:2048',
            'sensor_group_id' => 'integer'
        ]
    ];
}
