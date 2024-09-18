<?php

namespace Tobuli\Helpers\GeoLocation\GeoServices;


use Tobuli\Helpers\GeoLocation\Location;

interface GeoServiceInterface
{
    /**
     * @param $address
     * @return Location|null
     */
    public function byAddress($address);

    /**
     * @param $address
     * @return Location[]|null
     */
    public function listByAddress($address);

    /**
     * @param $lat
     * @param $lng
     * @return Location|null
     */
    public function byCoordinates($lat, $lng);
}