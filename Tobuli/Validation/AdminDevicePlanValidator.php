<?php namespace Tobuli\Validation;

use Tobuli\Entities\DevicePlan;

class AdminDevicePlanValidator extends Validator {
    public $rules = [
        'create' => [
            'title' => 'required|string',
            'duration_value' => 'required|numeric',
            'price' => 'required|numeric',
            'active' => 'required|boolean',
        ],
        'update' => [
            'id' => 'required|exists:device_plans',
            'title' => 'required|string',
            'duration_value' => 'required|numeric',
            'price' => 'required|numeric',
            'active' => 'required|boolean',
        ]
    ];

    public function validate($name, array $data, $id = NULL)
    {
        $this->rules[$name]['duration_type'] = 'required|in:'.implode(',', array_keys(DevicePlan::getDurationTypes()));

        parent::validate($name, $data, $id);
    }
}
