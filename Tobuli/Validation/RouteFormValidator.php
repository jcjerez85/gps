<?php namespace Tobuli\Validation;

class RouteFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            'polyline' => 'required',
            'color' => 'required|css_color'
        ],
        'update' => [
            'name' => 'required',
            'polyline' => 'required',
            'color' => 'required|css_color'
        ]
    ];

}   //end of class


//EOF