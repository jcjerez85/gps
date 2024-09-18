<?php namespace Tobuli\Validation;

class PoiFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required|max:255',
            'description' => 'max:3000',
            'map_icon_id' => 'required|exists:map_icons,id',
            'coordinates' => 'required|array',
            'coordinates.lat' => 'lat',
            'coordinates.lng' => 'lng',
        ],
        'update' => [
            'name' => 'max:255',
            'description' => 'max:3000',
            'map_icon_id' => 'exists:map_icons,id',
            'coordinates' => 'array',
            'coordinates.lat' => 'lat',
            'coordinates.lng' => 'lng',
        ]
    ];

}   //end of class


//EOF