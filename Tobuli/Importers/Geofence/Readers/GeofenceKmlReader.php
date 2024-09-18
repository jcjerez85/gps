<?php

namespace Tobuli\Importers\Geofence\Readers;

use Illuminate\Support\Arr;
use Tobuli\Importers\Readers\KmlReader;

class GeofenceKmlReader extends KmlReader
{
    static private $fields = ['name', 'styleUrl', 'coordinates'];

    private $rows = [];

    public function read($file)
    {
        $data = $this->getData($file);

        if (is_null($data)) {
            return null;
        }

        $folders = $this->parseElement($data, self::KEY_FOLDER);

        if ( ! empty($folders)) {

            if (Arr::isAssoc($folders))
                $folders = [$folders];

            foreach ($folders as $folder) {
                $groupName = $this->parseElement($folder, self::KEY_NAME);

                $placemarks = $this->parseElement($folder, self::KEY_PLACEMARK);

                $this->insertRows($placemarks, $groupName);
            }

            return $this->rows;
        }

        $placemarks = $this->parseElement($data, self::KEY_PLACEMARK);

        if (Arr::isAssoc($placemarks))
            $placemarks = [$placemarks];

        $this->insertRows($placemarks);

        return $this->rows;
    }

    private function insertRows($placemarks, $group = null)
    {
        // placemarks === placemark (single)
        if (array_key_exists(self::KEY_POLYGON, $placemarks)) {
            $this->insertRow($placemarks, $group);

            return;
        }

        // placemarks === placemark (single)
        if (array_key_exists(self::KEY_MULTIGEOMETRY, $placemarks)) {
            $this->insertRow($placemarks, $group);

            return;
        }

        // placemarks === placemark (single)
        if (array_key_exists(self::KEY_POLYLINE, $placemarks)) {
            $this->insertRow($placemarks, $group);

            return;
        }

        foreach ($placemarks as $key => $placemark) {
            $this->insertRow($placemark, $group);
        }
    }

    private function insertRow($placemark, $group)
    {
        if ( ! is_array($placemark)) {
            return;
        }

        $polygon = $this->parseElement($placemark, self::KEY_POLYGON);

        if (empty($polygon)) {
            $polygon = $this->parseElement($placemark, self::KEY_MULTIGEOMETRY);
        }

        if (empty($polygon)) {
            $polygon = $this->parseElement($placemark, self::KEY_POLYLINE);
        }

        if (empty($polygon)) {
            return;
        }

        $data = $this->simpleXMLElementToArray($placemark);
        $data[self::KEY_COORDINATES] = $this->searchCoordinates($data);

        if ( empty($data[self::KEY_COORDINATES])) {
            return;
        }

        if (empty($parsed = $this->parsePlacemark($data))) {
            return;
        }

        $parsed['group'] = $group;

        $this->rows[] = $this->applyStyles($parsed, ['color'], ['color' => 'polygon_color']);
    }

    private function parsePlacemark($data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if ( ! in_array($key, self::$fields, true)) {
                continue;
            }

            if ($key != self::KEY_COORDINATES) {
                $result[$key] = $value;
                continue;
            }

            $result['polygon'] = $this->parseCoordinates($value);
        }

        return $result;
    }

    protected function parseCoordinates($data)
    {
        $coordinates = [];
        $rows = explode(' ', $data);

        foreach ($rows as $row) {
            $coords = trim(preg_replace('/\t+/', '', $row));

            if ( ! $coords) {
                continue;
            }

            $coords = explode(',', $coords);

            if (isset($coords[0]) && isset($coords[1])) {
                $coordinates[] = [
                    'lat' => trim($coords[1]),
                    'lng' => trim($coords[0]),
                ];
            }
        }

        return $coordinates;
    }

    protected function searchCoordinates($data)
    {
        return $this->search($data, self::KEY_COORDINATES);
    }
}