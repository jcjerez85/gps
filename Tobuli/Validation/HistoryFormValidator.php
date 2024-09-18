<?php namespace Tobuli\Validation;

class HistoryFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'device_id' => 'required_without:imei',
            'from_date' => 'required|date',
            'to_date'   => 'required|date',
            'from_time' => 'required|regex:/^\d{2}:\d{2}(:\d{2})?$/',
            'to_time'   => 'required|regex:/^\d{2}:\d{2}(:\d{2})?$/',
        ]
    ];

}
