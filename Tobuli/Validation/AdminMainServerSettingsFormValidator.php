<?php

namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Illuminate\Validation\Rule;
use Tobuli\Helpers\LbsLocation\LbsManager;

class AdminMainServerSettingsFormValidator extends Validator
{
    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'update' => [
            'server_name' => 'required|max:255',
            'server_description' => 'required|max:300',
            'available_maps' => 'array|min:1',
            'default_map' => 'in_array:available_maps.*',

            'here_api_key' => 'required_if_in_array:available_maps,10,11,12',
            'mapbox_access_token' => 'required_if_in_array:available_maps,14,15,16',
            'bing_maps_key' => 'required_if_in_array:available_maps,7,8,9',
            'google_maps_key' => 'required_if_in_array:available_maps,1,3,4,5',
            'openmaptiles_url' => 'required_if_in_array:available_maps,21',
            'maptiler_key' => 'required_if_in_array:available_maps,17,18,19',
            'tomtom_key' => 'required_if_in_array:available_maps,26,27',

            'geocoders' => 'array',
            'geocoders.*.api' => 'present',
            'geocoders.*.api_url' => 'required_if:geocoders.*.api,nominatim|url',
            'geocoders.*.api_key' => 'required_if:geocoders.*.api,google,geocodio,pickpoint,here,mapmyindia,positionstack',
            'geocoders.*.api_app_id' => 'required_if:geocoders.*.api,mapmyindia',
            'geocoders.*.api_app_secret' => 'required_if:geocoders.*.api,mapmyindia',

            'device_cameras_days' => 'integer|min:0',
            'extra_expiration_time' => 'integer|min:0',

            'noreply_email' => 'email'
        ]
    ];

    public function __construct(IlluminateValidator $validator)
    {
        $lbsProviders = array_keys(LbsManager::PROVIDERS);

        $this->rules['update']['lbs'] = 'array';
        $this->rules['update']['lbs.provider'] = Rule::in($lbsProviders);
        $this->rules['update']['lbs.api_key'] = 'required_if:lbs.provider,' . implode(',', $lbsProviders);

        parent::__construct($validator);
    }
}
