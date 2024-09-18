<?php

/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.3.15
 * Time: 16.20
 */

namespace Tobuli\Validation;

use Tobuli\Entities\Task;

class TasksFormValidator extends Validator
{
    public $rules = [
        'create' => [
            'title' => 'required',
            'device_id' => 'required|exists:devices,id',
            'priority' => 'required',
            'pickup_address' => 'required',
            'pickup_address_lat' => 'required|lat',
            'pickup_address_lng' => 'required|lng',
            'pickup_time_from' => 'required|date',
            'pickup_time_to' => 'required|date|after:pickup_time_from',
            'delivery_address' => 'required',
            'delivery_address_lat' => 'required|lat',
            'delivery_address_lng' => 'required|lng',
            'delivery_time_from' => 'required|date',
            'delivery_time_to' => 'required|date|after:delivery_time_from',
        ],
        'update' => [
            'title' => 'required',
            'device_id' => 'required|exists:devices,id',
            'priority' => 'required',
            'pickup_address' => 'required',
            'pickup_address_lat' => 'required|lat',
            'pickup_address_lng' => 'required|lng',
            'pickup_time_from' => 'required|date',
            'pickup_time_to'   => 'required|date|after:pickup_time_from',
            'delivery_address' => 'required',
            'delivery_address_lat' => 'required|lat',
            'delivery_address_lng' => 'required|lng',
            'delivery_time_from' => 'required|date',
            'delivery_time_to' => 'required|date|after:delivery_time_from',
        ],
        'assign' => [
            'device_id' => 'required',
            'tasks' => 'required|array'
        ]
    ];

    public function validate($name, array $data, $id = NULL)
    {
        $this->rules[$name]['priority'] = 'required|in:'.implode(',', array_keys(Task::$priorities));

        parent::validate($name, $data, $id);
    }
}
