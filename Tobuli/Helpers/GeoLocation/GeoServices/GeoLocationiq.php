<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;


use Language;
use Tobuli\Helpers\GeoLocation\GeoSettings;

class GeoLocationiq extends GeoNominatim
{
    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $region = 'us1';

        $this->url = "https://{$region}.locationiq.com/v1/";
        $this->requestOptions = [
            'format'          => 'json',
            'key'             => $this->settings->getApiKey(),
            'accept-language' => Language::iso(),
            'addressdetails'  => 1,
        ];
    }
}