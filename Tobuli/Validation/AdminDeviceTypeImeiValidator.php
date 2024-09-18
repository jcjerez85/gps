<?php namespace Tobuli\Validation;

use Tobuli\Entities\DevicePlan;

class AdminDeviceTypeImeiValidator extends Validator {
    public $rules = [
        'create' => [
            'imei' => 'required|string|unique:device_type_imeis,imei',
            'device_type_id' => 'required|exists:device_types,id',
        ],
        'update' => [
            'imei' => 'required|string|unique:device_type_imeis,imei,%s',
            'device_type_id' => 'required|exists:device_types,id',
        ]
    ];
}
