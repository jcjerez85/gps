<?php

namespace Tobuli\Importers\POI\Readers;

use Illuminate\Support\Arr;
use Tobuli\Importers\Readers\KmlReader;

class POIKmlReader extends KmlReader
{
    public function read($file)
    {
        $data = $this->getData($file);

        if (is_null($data)) {
            return null;
        }

        $placemarks = $this->parseElement($data, self::KEY_PLACEMARK);

        if (Arr::isAssoc($placemarks))
            $placemarks = [$placemarks];

        $rows = [];

        foreach ($placemarks as $placemark) {
            $parsed = $this->parsePlacemark($placemark);

            if (empty($parsed)) {
                continue;
            }

            $parsed = $this->applyStyles($parsed, ['icon']);

            $rows[] = $parsed;
        }

        return $rows;
    }

    private function parsePlacemark($placemark)
    {
        if ( ! array_key_exists(self::KEY_POINT, $placemark)) {
            return null;
        }

        $result = [];

        $coordinates = $this->parseElement($placemark, 'coordinates');
        $coordinates = $this->parseCoordinates($coordinates);

        if ($coordinates) {
            $result = $placemark;
            $result['coordinates'] = $coordinates;
            unset($result['Point']);

            // $additionalData = $this->parseExtendedData($result); Won't be needed for now
            unset($result['ExtendedData']);
        }

        return $result;
    }

    private function parseExtendedData($data)
    {
        $result = [];

        if (isset($data['ExtendedData'])) {
            $extendedData = $this->parseElement($data['ExtendedData'], 'Data');

            foreach ($extendedData as $element) {
                $parsedElement = $this->parseAttribute($element);

                if ($parsedElement) {
                    $result = array_merge($result, $parsedElement);
                }
            }
        }

        return $result;
    }

    private function parseAttribute($data)
    {
        $result = [];
        $name = $data['@attributes']['name'] ?? null;
        $value = $data['value'] ?? null;

        if (isset($name) && isset($value)) {
            $result[$name] = $value;
        }

        return $result;
    }
}