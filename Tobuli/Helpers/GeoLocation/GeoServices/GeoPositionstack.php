<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;

use Language;
use Illuminate\Support\Arr;
use Tobuli\Helpers\GeoLocation\GeoSettings;
use Tobuli\Helpers\GeoLocation\Location;

class GeoPositionstack extends GeoNominatim
{
    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $this->url = 'http://api.positionstack.com/v1/';
        $this->requestOptions = [
            'access_key' => $this->settings->getApiKey(),
            'output'     => 'json',
            'language'   => Language::iso(),
        ];
    }

    public function byAddress($address)
    {
        $addresses = $this->request('forward', ['query' => $address]);

        return $addresses ? $this->locationObject($addresses[0]) : null;
    }

    public function listByAddress($address)
    {
        if ( ! $addresses = $this->request('forward', ['query' => $address])) {
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
        $address = $this->request('reverse', ['query' => "$lat,$lng"]);

        return $address ? $this->locationObject($address[0]) : null;
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
            throw new \Exception(Arr::get($response_body, 'error.message'));
        }

        return (is_array($response_body) && ! empty($response_body)) ? Arr::get($response_body, 'data') : null;
    }

    protected function locationObject($address)
    {
        return new Location([
            'place_id'      => md5(Arr::get($address, 'name')),
            'lat'           => Arr::get($address, 'latitude'),
            'lng'           => Arr::get($address, 'longitude'),
            'address'       => Arr::get($address, 'label'),
            'type'          => Arr::get($address, 'type'),
            'country'       => Arr::get($address, 'country'),
            'country_code'  => Arr::get($address, 'country_code'),
            'county'        => Arr::get($address, 'county'),
            'state'         => Arr::get($address, 'region'),
            'city'          => Arr::get($address, 'administrative_area'),
            'road'          => Arr::get($address, 'street'),
            'house'         => Arr::get($address, 'number'),
            'zip'           => Arr::get($address, 'postal_code'),
        ]);
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
            case 403:
                throw new \Exception('Geocoder API function does not supported by current subscription plan ');
                break;
            case 404:
                throw new \Exception('Unable to geocode');
                break;
            default:
                throw new \Exception('Geocoder API error. Code: ' . $status_code);
        }
    }
}