<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;

use Language;
use Illuminate\Support\Arr;
use Tobuli\Helpers\GeoLocation\GeoSettings;

class GeoPickpoint extends GeoNominatim
{
    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $this->url = 'https://api.pickpoint.io/v1/';
        $this->requestOptions = [
            'key'             => $this->settings->getApiKey(),
            'format'          => 'json',
            'accept-language' => Language::iso(),
            'addressdetails'  => 1,
        ];
    }

    public function byAddress($address)
    {
        $addresses = $this->request('forward', ['q' => $address]);

        return $addresses ? $this->locationObject($addresses[0]) : null;
    }

    public function listByAddress($address)
    {
        if ( ! $addresses = $this->request('forward', ['q' => $address])) {
            return [];
        }

        $locations = [];

        foreach ($addresses as $address) {
            $locations[] = $this->locationObject($address);
        }

        return $locations;
    }


    public function byCoordinates($lat, $lng)
    {
        $address = $this->request('reverse', ['lat' => $lat, 'lon' => $lng]);

        return $address ? $this->locationObject($address) : null;
    }

    protected function request($method, $options)
    {
        $response = $this->curl->get(
            trim($this->url, '/') . '/' . $method,
            array_merge($options, $this->requestOptions)
        );

        if ( ! in_array($response->headers['Status-Code'], [200])) {
            $this->throwException($response->headers['Status-Code']);
        }

        $response_body = json_decode($response->body, true);

        if (empty($response_body))
            $this->throwException(404);

        if (array_key_exists('error', $response_body)) {
            throw new \Exception(Arr::get($response_body, 'error'));
        }

        return (is_array($response_body) && ! empty($response_body)) ? $response_body : null;
    }


    protected function locationObject($address)
    {
        $location = parent::locationObject($address);

        $location->address = $location->buildDisplayName([
            'road',
            'house',
            'zip',
            'city',
            'county',
            'state',
            'country',
        ]);

        return $location;
    }
}