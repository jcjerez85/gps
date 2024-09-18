<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Tobuli\Services\RequiredFields\DeviceRequiredFieldsService;

class DeviceFormValidator extends Validator
{

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'imei'                => 'required|unique:devices,imei,%s',
            'name'                => 'required',
            'icon_id'             => 'required|exists:device_icons,id',
            'fuel_quantity'       => 'numeric',
            'fuel_price'          => 'numeric',
            'fuel_measurement_id' => 'required|in:1,2,3,4,5',
            'tail_length'         => 'required|numeric|min:0|max:10',
            'min_moving_speed'    => 'required|numeric|min:1|max:50',
            'min_fuel_fillings'   => 'required|numeric|min:1|max:1000',
            'min_fuel_thefts'     => 'required|numeric|min:1|max:1000',
            'group_id'            => 'exists:device_groups,id',
            'sim_number'          => 'unique:devices,sim_number',
            'installation_date'   => 'date',
            'sim_activation_date' => 'date',
            'sim_expiration_date' => 'date',
            'forward.protocol'    => 'required_if:forward.active,1|in:TCP,UDP',
            'msisdn'              => 'sometimes|regex:/^\d{6,20}$/',
        ],
        'update' => [
            'imei'                => 'sometimes|required|unique:devices,imei,%s',
            'name'                => 'required',
            'icon_id'             => 'exists:device_icons,id',
            'fuel_quantity'       => 'numeric',
            'fuel_price'          => 'numeric',
            'fuel_measurement_id' => 'required|in:1,2,3,4,5',
            'tail_length'         => 'numeric|min:0|max:10',
            'min_moving_speed'    => 'numeric|min:1|max:50',
            'min_fuel_fillings'   => 'numeric|min:1|max:1000',
            'min_fuel_thefts'     => 'numeric|min:1|max:1000',
            'group_id'            => 'exists:device_groups,id',
            'sim_number'          => 'unique:devices,sim_number,%s',
            'installation_date'   => 'date',
            'sim_activation_date' => 'date',
            'sim_expiration_date' => 'date',
            'forward.protocol'    => 'required_if:forward.active,1|in:TCP,UDP',
            'msisdn'              => 'sometimes|regex:/^\d{6,20}$/',
        ],
    ];

    public function __construct(IlluminateValidator $validator)
    {
        $this->_validator = $validator;

        $this->rules['create']['group_id'] = 'nullable|exists:device_groups,id,user_id,' . auth()->user()->id;
        $this->rules['update']['group_id'] = 'nullable|exists:device_groups,id,user_id,' . auth()->user()->id;

        $maxIps = config('tobuli.limits.forward_ips');
        $this->rules['create']['forward.ip'] = "required_if:forward.active,1|semicolon_array:array_max:$maxIps|semicolon_element:host_port";
        $this->rules['update']['forward.ip'] = "required_if:forward.active,1|semicolon_array:array_max:$maxIps|semicolon_element:host_port";

        $extraRules = (new DeviceRequiredFieldsService())->getRules();

        appendRulesArray($this->rules['create'], $extraRules);
        appendRulesArray($this->rules['update'], $extraRules);
    }

}   //end of class

//EOF