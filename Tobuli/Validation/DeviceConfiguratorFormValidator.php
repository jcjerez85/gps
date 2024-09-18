<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class DeviceConfiguratorFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'configure' => [
            'config_id' => 'required|exists:device_config,id',
            'device_id' => 'required_unless:configure_device,1|exists:devices,id',
            'sim_number' => 'required_if:configure_device,1',
        ],
    ];
}
