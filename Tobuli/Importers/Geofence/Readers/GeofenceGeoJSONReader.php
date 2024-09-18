<?php

namespace Tobuli\Importers\Geofence\Readers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tobuli\Importers\Readers\GeoJSONReader;

class GeofenceGeoJSONReader extends GeoJSONReader
{
    protected function parsePoint($data)
    {
        $result = null;

        $properties = $this->parseElement($data, self::KEY_PROPERTIES);
        $coordinates = $this->parseElement($data, self::KEY_COORDINATES);
        $coordinates = $this->parseCoordinates($coordinates);

        if ($properties && ! empty($coordinates)) {
            $result = $properties + ['polygon' => $coordinates] + $this->parseColor($data);
        }

        return $result;
    }

    protected function parseCoordinates($data)
    {
        $result = [];

        if (isset($data[0])) {
            $coordinates = Arr::collapse($data);

            foreach ($coordinates as $coordinate) {
                $result[] = [
                    'lat' => $coordinate[1],
                    'lng' => $coordinate[0],
                ];
            }
        }

        return $result;
    }

    private function parseColor($data)
    {
        $result = ['polygon_color' => '#ffffff'];
        $style = $this->parseElement($data, self::KEY_STYLE);

        if ( ! isset($style['fill'])) {
            return $result;
        }

        if ( ! Str::startsWith($style['fill'], '#')) {
            $style['fill'] = '#' . $style['fill'];
        }

        $result['polygon_color'] = $style['fill'];
        $result['polygon_color'] = substr($result['polygon_color'], 0, 7);

        return $result;
    }
}
