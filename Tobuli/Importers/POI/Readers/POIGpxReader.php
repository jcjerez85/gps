<?php

namespace Tobuli\Importers\POI\Readers;

use Tobuli\Importers\Readers\GpxReader;

class POIGpxReader extends GpxReader
{
    public function read($file)
    {
        $data = $this->getData($file);

        if (is_null($data)) {
            return null;
        }

        $rows = [];

        $waypoints = $this->parseElement($data, self::KEY_WPT);

        foreach ($waypoints as $waypoint) {
            $data = $this->simpleXMLElementToArray($waypoint);
            $data = $this->manageCoordinates($data);
            $data['description'] = $data['desc'] ?? '';

            unset($data['desc']);

            $rows[] = $data;
        }

        return $rows;
    }

    private function manageCoordinates($data)
    {
        if (isset($data['lat']) && isset($data['lon'])) {
            $data['coordinates'] = [
                'lat' => $data['lat'],
                'lng' => $data['lon'],
            ];
            unset($data['lat'], $data['lon']);
        }

        return $data;
    }
}