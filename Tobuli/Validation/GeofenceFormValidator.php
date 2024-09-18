<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Illuminate\Validation\Rule;

class GeofenceFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name'          => 'required|string',
            'type'          => 'required|in:polygon,circle',
            'polygon'       => 'required_if:type,polygon|array',
            'polygon.*.lat' => 'required_if:type,polygon|lat',
            'polygon.*.lng' => 'required_if:type,polygon|lng',
            'center'        => 'required_if:type,circle',
            'center.lat'    => 'required_if:type,circle|lat',
            'center.lng'    => 'required_if:type,circle|lng',
            'radius'        => 'required_if:type,circle|numeric',
            'polygon_color' => 'required|min:7|max:7',
            'speed_limit'   => 'numeric',
        ],
        'update' => [
            'name'          => 'string',
            'type'          => 'in:polygon,circle',
            'polygon'       => 'required_if:type,polygon|array',
            'polygon.*.lat' => 'required_if:type,polygon|lat',
            'polygon.*.lng' => 'required_if:type,polygon|lng',
            'center'        => 'required_if:type,circle',
            'center.lat'    => 'required_if:type,circle|lat',
            'center.lng'    => 'required_if:type,circle|lng',
            'radius'        => 'required_if:type,circle|numeric|nullable',
            'polygon_color' => 'min:7|max:7',
            'speed_limit'   => 'numeric',
        ],
    ];

    public function __construct( IlluminateValidator $validator ) {
        $this->_validator = $validator;

        $rule = Rule::exists('geofence_groups', 'id')->where(function($query) {
            $query->where('user_id', auth()->user()->id);
        });

        $this->rules['create']['group_id'] = [$rule, 'nullable'];
        $this->rules['update']['group_id'] = [$rule, 'nullable'];


    }

}   //end of class


//EOF