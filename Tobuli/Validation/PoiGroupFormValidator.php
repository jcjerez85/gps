<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class PoiGroupFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'title' => 'required|max:255',
            'pois'  => 'required|array|exists:user_map_icons,id',
        ],
        'update' => [
            'title' => 'max:255',
            'pois'  => 'array|exists:user_map_icons,id'
        ]
    ];

}   //end of class


//EOF