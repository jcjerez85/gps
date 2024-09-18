<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;

use Language;
use Illuminate\Support\Arr;
use Tobuli\Helpers\GeoLocation\GeoSettings;
use Tobuli\Helpers\GeoLocation\Location;

class GeoNominatim extends AbstractGeoService
{
    protected $url;
    protected $curl;
    protected $requestOptions = [];

    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $curl = new \Curl;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;
        $curl->options['CURLOPT_TIMEOUT'] = 5;

        $this->curl = $curl;
        $this->url = $this->settings->getApiUrl();
        $this->requestOptions = [
            'format'          => 'json',
            'accept-language' => Language::iso(),
            'addressdetails'  => 1,
        ];
    }


    public function byAddress($address)
    {
        $addresses = $this->request('search', ['q' => $address]);

        return $addresses ? $this->locationObject($addresses[0]) : null;
    }

    public function listByAddress($address)
    {
        if ( ! $addresses = $this->request('search', ['q' => $address])) {
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
            trim($this->url, '/') . '/' . $method . '.php',
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
        return new Location([
            'place_id'      => Arr::get($address, 'place_id'),
            'lat'           => Arr::get($address, 'lat'),
            'lng'           => Arr::get($address, 'lon'),
            'address'       => Arr::get($address, 'display_name'),
            'type'          => Arr::get($address, 'osm_type'),
            'country'       => $this->getFirst($address['address'], ['country']),
            'country_code'  => $this->getFirst($address['address'], ['country_code']),
            'county'        => $this->getFirst($address['address'], ['county']),
            'state'         => $this->getFirst($address['address'], ['state', 'region']),
            'city'          => $this->getFirst($address['address'], ['city', 'town', 'village', 'municipality', 'city_district']),
            'road'          => $this->getFirst($address['address'], ['road']),
            'house'         => $this->getFirst($address['address'], ['house_number', 'house_name']),
            'zip'           => $this->getFirst($address['address'], ['postcode']),
        ]);
    }

    protected function getFirst(array $source, array $fields)
    {
        foreach ($fields as $field) {
            $value = Arr::get($source, $field);

            if (!empty($value))
                return $value;
        }

        return null;
    }

    protected function throwException($status_code)
    {
        switch ($status_code) {
            case 429:
                throw new \Exception('Geocoder API request limit exceeded.');
                break;
            case 401:
                throw new \Exception('Geocoder API Key is invalid or inactive');
                break;
            case 404:
                throw new \Exception('Unable to geocode');
                break;
            default:
                throw new \Exception('Geocoder API error. Code: ' . $status_code);
        }
    }
}