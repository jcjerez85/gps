<?php namespace Tobuli\Validation;

class DeviceExpensesFormValidator extends Validator
{
    public $rules = [
        'create' => [
            'name'      => 'required',
            'type_id'   => 'exists:device_expense_types,id',
            'device_id' => 'required|integer',
            'quantity'  => 'required|numeric',
            'unit_cost' => 'required|numeric',
            'date'      => 'required',
        ],
        'update' => [
            'name'      => 'required',
            'type_id'   => 'exists:device_expense_types,id',
            'quantity'  => 'required|numeric',
            'unit_cost' => 'required|numeric',
            'date'      => 'required',
        ],
    ];

}   //end of class

//EOF