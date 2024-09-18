<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Tobuli\Entities\DeviceRouteType;

class DeviceRoutesTypeFormValidator extends Validator
{
    public $rules = [
        'create' => [
            'started_at' => 'required|date|before:ended_at',
            'ended_at'   => 'required|date|after:started_at',
            'type'       => 'required',
        ],
        'update' => [
            'started_at' => 'required|date|before:ended_at',
            'ended_at'   => 'required|date|after:started_at',
            'type'       => 'required',
        ],
    ];

    public function __construct( IlluminateValidator $validator ) {
        $this->_validator = $validator;

        $types = array_keys(DeviceRouteType::types());

        $this->rules['create']['type'] = 'required|in:'.implode(',', $types);
        $this->rules['update']['type'] = 'required|in:'.implode(',', $types);
    }

}   //end of class

//EOF