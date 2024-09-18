<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;

use Language;
use Illuminate\Support\Arr;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\GeoLocation\GeoSettings;
use Tobuli\Helpers\GeoLocation\Location;


class GeoHere extends AbstractGeoService
{
    private $curl;
    private $urls;
    private $requestOptions = [];


    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $curl = new \Curl;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;
        $curl->options['CURLOPT_TIMEOUT'] = 5;

        $this->curl = $curl;

        $this->urls = [
            "reverse" => "https://reverse.geocoder.ls.hereapi.com/6.2/reversegeocode.json",
            "geocode" => "https://geocoder.ls.hereapi.com/6.2/geocode.json"
        ];

        $this->requestOptions = [
            'apiKey' => $this->settings->getApiKey(),
            'language' => Language::iso(),
        ];
    }


    public function byAddress($address)
    {
        $address = $this->request(
            'geocode',
            [
                'searchtext' => $address,
                'maxresults' => 1,
            ]
        );

        return $address ? $this->locationObject($address) : null;
    }


    public function listByAddress($address)
    {
        $addresses = $this->request(
            'geocode',
            [
                'searchtext' => $address,
                'maxresults' => 10
            ]
        );

        $locations = [];

        if (empty($addresses)) {
            return $locations;
        }

        if (isset($addresses['Location'])) {
            return [$this->locationObject($addresses)];
        }

        foreach ($addresses as $address) {
            $locations[] = $this->locationObject($address);
        }

        return $locations;
    }


    public function byCoordinates($lat, $lng)
    {
        $address = $this->request(
            'reverse',
            [
                'prox' => $lat . ',' . $lng . ',250',
                'mode' => 'retrieveAddresses',
                'maxresults' => '1',
            ]
        );

        return $address ? $this->locationObject($address) : null;
    }

    private function request($method, $options)
    {
        $response = $this->curl->get(
            $this->urls[$method],
            array_merge($options, $this->requestOptions)
        );

        $response_body = json_decode($response->body, true);

        if ($response->headers['Status-Code'] != 200 || $response_body == null)
            throw new \Exception('Geocoder API error.');

        $views = $response_body["Response"]["View"];
        if (empty($views))
            return null;

        $results = $views[0]["Result"];
        if (empty($results))
            return null;

        if (count($results) == 1)
            return $results[0];

        return $results;
    }

    private function locationObject($address)
    {
        $countryName = Arr::get($address, 'Address.Country');

        foreach (Arr::get($address, 'Location.Address.AdditionalData') as $data) {
            if($data["key"] == "CountryName")
                $countryName = $data["value"];
        }

        return new Location([
            'place_id'      => Arr::get($address, 'Location.LocationId'),
            'lat'           => Arr::get($address, 'Location.DisplayPosition.Latitude'),
            'lng'           => Arr::get($address, 'Location.DisplayPosition.Longitude'),
            'address'       => Arr::get($address, 'Location.Address.Label'),
            'country'       => $countryName,
            'country_code'  => Arr::get($address, 'Location.Address.Country'),
            'state'         => Arr::get($address, 'Location.Address.State'),
            'county'        => Arr::get($address, 'Location.Address.County'),
            'city'          => Arr::get($address, 'Location.Address.City'),
            'road'          => Arr::get($address, 'Location.Address.Street'),
            'house'         => Arr::get($address, 'Location.Address.HouseNumber'),
            'zip'           => Arr::get($address, 'Location.Address.PostalCode'),
            'type'          => Arr::get($address, 'MatchType'),
        ]);
    }
}