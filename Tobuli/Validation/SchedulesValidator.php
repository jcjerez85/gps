<?php

namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Tobuli\Entities\Schedule;

class SchedulesValidator extends Validator
{
    public $rules = [
        'create' => [
            'schedule_type' => 'required',
            'exact_time.time' => 'required_if:schedule_type,exact_time|date',
            'hourly.minute' => 'required_if:schedule_type,hourly|integer|between:0,59',
            'daily.time' => 'required_if:schedule_type,daily',
            'monthly.day' => 'required_if:schedule_type,monthly',
            'monthly.time' => 'required_if:schedule_type,monthly',
        ],
        'update' => [
            'schedule_type' => 'required',
            'exact_time.time' => 'required_if:schedule_type,exact_time|date',
            'hourly.minute' => 'required_if:schedule_type,hourly|integer|between:0,59',
            'daily.time' => 'required_if:schedule_type,daily',
            'monthly.day' => 'required_if:schedule_type,monthly',
            'monthly.time' => 'required_if:schedule_type,monthly',
        ],
    ];
}