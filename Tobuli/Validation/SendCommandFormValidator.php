<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class SendCommandFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [

    ];

    function __construct( IlluminateValidator $validator ) {
        parent::__construct( $validator );

        $commandDevices = config('tobuli.limits.command_devices');

        $this->rules = [
            'create' => [
                'devices' => 'required|array' . ($commandDevices ? "|array_max:$commandDevices" : ""),
                'message' => 'required',
                'gprs_template_id' => 'required_if:type,template'
            ],
            'update' => [
            ]
        ];
    }

}
