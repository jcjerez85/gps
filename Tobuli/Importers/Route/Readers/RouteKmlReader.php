<?php

namespace Tobuli\Importers\Route\Readers;

use Illuminate\Support\Arr;
use Tobuli\Importers\Readers\KmlReader;

class RouteKmlReader extends KmlReader
{
    static private $fields = ['name', 'styleUrl', 'coordinates', 'color'];

    private $rows = [];

    public function read($file)
    {
        $data = $this->getData($file);

        if (is_null($data)) {
            return null;
        }

        $folders = $this->parseElement($data, self::KEY_FOLDER);

        if (Arr::isAssoc($folders))
            $folders = [$folders];

        if ( ! empty($folders)) {
            foreach ($folders as $folder) {
                $placemarks = $this->parseElement($folder, self::KEY_PLACEMARK);

                $this->insertRows($placemarks);
            }

            return $this->rows;
        }

        $placemarks = $this->parseElement($data, self::KEY_PLACEMARK);

        if (Arr::isAssoc($placemarks))
            $placemarks = [$placemarks];

        $this->insertRows($placemarks);

        return $this->rows;
    }

    private function insertRows($placemarks)
    {
        // placemarks === placemark (single)
        if (array_key_exists(self::KEY_POLYLINE, $placemarks)) {
            $this->insertRow($placemarks);

            return;
        }

        foreach ($placemarks as $key => $placemark) {
            $this->insertRow($placemark);
        }
    }

    private function insertRow($placemark)
    {
        if ( ! is_array($placemark)) {
            return;
        }

        $polyline = $this->parseElement($placemark, self::KEY_POLYLINE);

        if (empty($polyline)) {
            return;
        }

        $data = $this->simpleXMLElementToArray($placemark);



        if ( ! isset($data['coordinates'])) {
            return;
        }

        if (empty($parsed = $this->parsePlacemark($data))) {
            return;
        }

        $this->rows[] = $this->applyStyles($parsed, ['color']);
    }

    private function parsePlacemark($data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if ( ! in_array($key, self::$fields)) {
                continue;
            }

            if ($key != self::KEY_COORDINATES) {
                $result[$key] = $value;
                continue;
            }

            $result['polyline'] = $this->parseCoordinates($value);
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
}