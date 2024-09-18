<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;



use Tobuli\Helpers\GeoLocation\GeoSettings;

class GeoOpenstreet extends GeoNominatim
{
    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $this->url = 'https://nominatim.openstreetmap.org/';
    }
}