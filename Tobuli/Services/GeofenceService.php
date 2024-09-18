<?php

namespace Tobuli\Services;

use Tobuli\Entities\Geofence;
use CustomFacades\Validators\GeofenceFormValidator;

class GeofenceService extends ModelService
{
    public function __construct()
    {
        $this->setDefaults([
            'active'        => true,
            'type'          => 'polygon',
            'polygon_color' => '#d000df',
        ]);

        $this->setValidationRulesStore(
            GeofenceFormValidator::getFacadeRoot()->rules['create']
        );

        $this->setValidationRulesUpdate(
            GeofenceFormValidator::getFacadeRoot()->rules['update']
        );
    }

    public function store(array $data)
    {
        return Geofence::create($data);
    }

    public function update($geofence, array $data)
    {
        return $geofence->update($data);
    }

    public function delete($geofence)
    {
        return $geofence->delete();
    }

    /**
     * @param $data
     */
    protected function normalize(array $data)
    {
        if (array_key_exists('group_id', $data)) {
            if (empty($data['group_id'])) {
                $data['group_id'] = null;
            }
        }

        if (array_key_exists('polygon', $data)) {
            if (is_string($data['polygon'])) {
                $data['polygon'] = json_decode($data['polygon'], TRUE);
            }

            if (empty($data['polygon'])) {
                $data['polygon'] = [];
            }

            $data['polygon'] = array_map(function($point) {
                return [
                    'lat' => floatval($point['lat']),
                    'lng' => floatval($point['lng'])
                ];
            }, $data['polygon']);
        }

        if (array_key_exists('center', $data)) {
            if (is_string($data['center'])) {
                $data['center'] = json_decode($data['center'], TRUE);
            }
        }

        if (array_key_exists('speed_limit', $data)) {
            $data['speed_limit'] = \Formatter::speed()->reverse($data['speed_limit']);
        }

        return $data;
    }



    /**
     * @param Geofence $geofence
     * @param $point
     * @return Geofence
     */
    public function moveTo(Geofence $geofence, $point)
    {
        if ($geofence->type == Geofence::TYPE_CIRCLE)
            return $this->moveCircleTo($geofence, $point);
        else
            return $this->movePolygonTo($geofence, $point);
    }

    /**
     * @param Geofence $geofence
     * @param $point
     * @return Geofence
     */
    public function moveCircleTo(Geofence $geofence, $point)
    {
        $geofence->center = [
            'lat' => $point['lat'],
            'lng' => $point['lng']
        ];

        return $geofence;
    }

    /**
     * @param Geofence $geofence
     * @param $point
     * @return Geofence
     */
    public function movePolygonTo(Geofence $geofence, $point)
    {
        $newCoordinates = [];

        $center = $geofence->getCenter();

        if (is_null($center))
            return $geofence;

        $coordinates = json_decode($geofence->coordinates, true);

        foreach ($coordinates as $coordinate) {
            $newCoordinates[] = [
                'lat' => $coordinate['lat'] - $center['lat'],
                'lng' => $coordinate['lng'] - $center['lng'],
            ];
        }

        array_walk($newCoordinates, function(&$coordinate, $key) use ($point) {
            $coordinate = [
                'lat' => $coordinate['lat'] + $point['lat'],
                'lng' => $coordinate['lng'] + $point['lng'],
            ];
        });

        $geofence->coordinates = json_encode($newCoordinates);

        return $geofence;
    }

    public function calculateSwNeBounds(Geofence $geofence): bool
    {
        if ($geofence->type === Geofence::TYPE_CIRCLE) {
            return false;
        }

        $swLat = 90;
        $neLat = -90;
        $swLng = 180;
        $neLng = -180;

        foreach (json_decode($geofence->coordinates, true) as $coordinate) {
            $lat = $coordinate['lat'];
            $lng = $coordinate['lng'];

            if ($lat < $swLat) {
                $swLat = $lat;
            }

            if ($lat > $neLat) {
                $neLat = $lat;
            }

            if ($lng < $swLng) {
                $swLng = $lng;
            }

            if ($lng > $neLng) {
                $neLng = $lng;
            }
        }

        $geofence->sw_lat = $swLat;
        $geofence->ne_lat = $neLat;
        $geofence->sw_lng = $swLng;
        $geofence->ne_lng = $neLng;

        return true;
    }
}