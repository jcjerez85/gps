<?php

namespace Tobuli\Importers\POI\Readers;

use Tobuli\Importers\Readers\GeoJSONReader;

class POIGeoJSONReader extends GeoJSONReader
{
    protected function parsePoint($data)
    {
        $result = null;

        if (isset($data['geometry']['coordinates'])) {
            $result['coordinates'] = $this->parseCoordinates($data['geometry']['coordinates']);
            $result = array_replace($result, $data['properties']);
        }

        return $result;
    }

    protected function parseCoordinates($data)
    {
        $coordinates = [];

        if (isset($data[0]) && isset($data[1])) {
            $coordinates = [
                'lat' => trim($data[1]),
                'lng' => trim($data[0]),
            ];
        }

        return $coordinates;
    }
}