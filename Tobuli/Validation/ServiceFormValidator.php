<?php namespace Tobuli\Validation;

class ServiceFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            'interval' => 'required|numeric|min:1',
            //'last_service' => 'numeric|min:0',
            'trigger_event_left' => 'required|numeric|min:1|lesser_than:interval',
            'description' => 'string|max:255'
        ],
        'update' => [
            'name' => 'required',
            'interval' => 'required|numeric|min:1',
            //'last_service' => 'numeric|min:0',
            'trigger_event_left' => 'required|numeric|min:1|lesser_than:interval',
            'description' => 'string|max:255'
        ],
    ];
}   //end of class


//EOF