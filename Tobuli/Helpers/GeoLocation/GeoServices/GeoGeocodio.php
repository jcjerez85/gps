<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;

use Geocodio\Geocodio;
use Tobuli\Helpers\GeoLocation\GeoSettings;
use Tobuli\Helpers\GeoLocation\Location;

class GeoGeocodio extends AbstractGeoService
{
    private $client;


    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $this->client = (new Geocodio())->setApiKey($this->settings->getApiKey());
    }


    public function byAddress($address)
    {
        $addresses = $this->client->geocode($address)->response->results;

        return (! empty($addresses)) ? $this->locationObject($addresses[0]) : null;
    }


    public function listByAddress($address)
    {
        if (empty($addresses = $this->client->geocode($address)->response->results)) {
            return [];
        }

        $locations = [];

        foreach (array_slice($addresses, 0, 10) as $address) {
            $locations[] = $this->locationObject($address);
        }

        return $locations;
    }


    public function byCoordinates($lat, $lng)
    {
        $addresses = $this->client->reverse($lat . ',' . $lng)->response->results;

        return (! empty($addresses)) ? $this->locationObject($addresses[0]) : null;
    }


    private function locationObject($address)
    {
        return new Location([
            'place_id'      => isset($address->formatted_address) ? md5($address->formatted_address) : null,
            'lat'           => isset($address->location->lat) ? $address->location->lng : null,
            'lng'           => isset($address->location->lng) ? $address->location->lng : null,
            'address'       => isset($address->formatted_address) ? $address->formatted_address : null,
            'country_code'  => isset($address->address_components->country) ? $address->address_components->country : null,
            'state'         => isset($address->address_components->state) ? $address->address_components->state : null,
            'county'        => isset($address->address_components->county) ? $address->address_components->county : null,
            'city'          => isset($address->address_components->city) ? $address->address_components->city : null,
            'road'          => isset($address->address_components->street) ? $address->address_components->street : null,
            'zip'           => isset($address->address_components->zip) ? $address->address_components->zip : null,
            'house'         => isset($address->address_components->number) ? $address->address_components->number : null,
            'type'          => isset($address->accuracy_type) ? $address->accuracy_type : null,
        ]);
    }
}