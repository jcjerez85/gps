<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Illuminate\Validation\Rule;
use Tobuli\Reports\ReportManager;

class ReportFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'devices' => 'required_unless:type,35,36,38|array',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'speed_limit' => 'numeric',
            'geofences' => 'array',
        ]
    ];

    public function __construct(IlluminateValidator $validator)
    {
        parent::__construct($validator);

        $this->rules['create']['format'] = 'required|' . Rule::in(array_keys(ReportManager::getFormats()));
    }

}   //end of class


//EOF