<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;

use Illuminate\Support\Facades\Cache;
use Tobuli\Helpers\GeoLocation\GeoSettings;
use Tobuli\Helpers\GeoLocation\Location;

class GeoMapmyindia extends AbstractGeoService
{
    private $curl;

    private $urls = [];

    public function __construct(GeoSettings $settings)
    {
        parent::__construct($settings);

        $this->curl = new \Curl;
        $this->curl->options['CURLOPT_SSL_VERIFYPEER'] = false;
        $this->curl->options['CURLOPT_TIMEOUT'] = 5;

        //For some reason geocode uses OAuth2 whilst reverse geocoding uses simple api key...
        $this->urls = [
            'geocode' => 'https://atlas.mapmyindia.com/api/places/geocode',
            'reverse' => 'https://apis.mapmyindia.com/advancedmaps/v1/' . $this->settings->getApiKey() . '/rev_geocode',
        ];
    }

    public function byAddress($address)
    {
        $location = $this->request(
            'geocode',
            [
                'address'   => $address,
                'itemCount' => 1,
            ]
        );

        return $this->locationObjectFromGeo($location);
    }

    public function listByAddress($address)
    {
        $locations = $this->request(
            'geocode',
            [
                'address'   => $address,
                'itemCount' => 10,
            ]
        );

        $results = [];
        foreach ($locations as $location) {
            $results[] = $this->locationObjectFromGeo($location);
        }

        return $results;
    }

    public function byCoordinates($lat, $lng)
    {
        $location = $this->request(
            'reverse',
            [
                'lat'       => $lat,
                'lng'       => $lng,
                'itemCount' => 1,
            ]
        );

        return $this->locationObjectFromGeoOld($location);
    }

    private function request($method, $options)
    {
        if ($method == 'geocode')
            $this->curl->headers['Authorization'] = $this->getOAuthHeader();

        $response = $this->curl->get(
            $this->urls[$method],
            $options
        );

        $response_body = json_decode($response->body, true);

        if ($response->headers['Status-Code'] != 200)
            throw new \Exception('Geocoder API error.');

        if ($response_body == null)
            throw new \Exception('Location not found! (MapMyIndia only supports Indian locations!).');

        if ($method == 'geocode')
            return $response_body['copResults'];

        $result = $response_body['results'];
        if (empty($result))
            throw new \Exception('Location not found! (MapMyIndia only supports Indian locations!).');

        return $result[0];
    }

    private function locationObjectFromGeo($address)
    {
        return new Location([
            'place_id'     => $address['eLoc'],
            'lat'          => $address['latitude'],
            'lng'          => $address['longitude'],
            'address'      => $address['formattedAddress'],
            'country'      => 'India',
            'country_code' => 'IND',
            'state'        => $address['state'],
            'county'       => null,
            'city'         => $address['city'],
            'road'         => $address['street'],
            'house'        => $address['houseNumber'],
            'zip'          => $address['pincode'],
            'type'         => $address['confidenceScore'],
        ]);
    }

    private function locationObjectFromGeoOld($address)
    {
        $formattedAddress = $address['formatted_address'];

        return new Location([
            'place_id'     => md5($formattedAddress),
            'lat'          => $address['lat'],
            'lng'          => $address['lng'],
            'address'      => $formattedAddress,
            'country'      => $address['area'],
            'country_code' => '',
            'state'        => $address['state'],
            'county'       => null,
            'city'         => $address['city'],
            'road'         => $address['street'],
            'house'        => $address['houseNumber'],
            'zip'          => $address['pincode'],
            'type'         => 1,
        ]);
    }

    private function getOAuthHeader() {
        if(Cache::has('geo_mapmyindia_token'))
            $data = Cache::get('geo_mapmyindia_token');

        else {
            $requestParams = [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->settings->getAppId(),
                'client_secret' => $this->settings->getAppSecret(),
            ];

            $response = $this->curl->post(
                'https://outpost.mapmyindia.com/api/security/oauth/token',
                $requestParams
            );

            $responseBody = json_decode($response->body, true);

            if (array_key_exists('error', $responseBody))
                throw new \Exception($responseBody['error_description']);

            $data = [
                "type"  => $responseBody['token_type'],
                "token" => $responseBody['access_token']
            ];

            //Time To Live
            $ttl = floor($responseBody['expires_in']);

            Cache::put('geo_mapmyindia_token', $data, $ttl);
        }

        return $data['type'] . ' ' . $data['token'];
    }
}