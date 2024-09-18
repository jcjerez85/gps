<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;


use Tobuli\Helpers\GeoLocation\GeoSettings;

class GeoDefault extends GeoNominatim
{
    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $servers = config('services.nominatims');

        $this->url = $servers[ rand(0, count($servers) - 1) ];
    }
}