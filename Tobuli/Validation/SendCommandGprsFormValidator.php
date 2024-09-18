<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class SendCommandGprsFormValidator extends Validator
{

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'device_id' => 'required|array',
            'type'      => 'required'
        ],
        'commands' => [
            'device_id' => 'required'
        ],
    ];

    function __construct(IlluminateValidator $validator)
    {
        parent::__construct($validator);

        $limit = config('tobuli.limits.command_gprs_devices');

        if ($limit) {
            $this->rules['create']['device_id'] .= "|array_max:$limit";
        }
    }
}