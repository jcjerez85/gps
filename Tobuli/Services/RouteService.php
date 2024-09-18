<?php

namespace Tobuli\Services;

use CustomFacades\Validators\RouteFormValidator;
use Tobuli\Entities\Route;

class RouteService extends ModelService
{
    public function __construct()
    {
        $this->setDefaults([
            'active' => true,
        ]);

        $this->setValidationRulesStore(
            RouteFormValidator::getFacadeRoot()->rules['create']
        );

        $this->setValidationRulesUpdate(
            RouteFormValidator::getFacadeRoot()->rules['update']
        );
    }

    public function store(array $data)
    {
        $item = new Route($data);
        $item->coordinates = $data['coordinates'] ?? null;
        $item->save();

        return $item;
    }

    public function update($route, array $data)
    {
        if (isset($data['coordinates'])) {
            $route->coordinates = $data['coordinates'];
        }

        return $route->update($data);
    }

    public function delete($route)
    {
        return $route->delete();
    }

    protected function normalize(array $data)
    {
        if (array_key_exists('coordinates', $data)) {
            if (is_string($data['coordinates'])) {
                $data['coordinates'] = json_decode($data['coordinates'], true);
            }

            if (empty($data['coordinates'])) {
                $data['coordinates'] = [];
            }

            $data['coordinates'] = array_map(function($point) {
                return [
                    'lat' => floatval($point['lat']),
                    'lng' => floatval($point['lng'])
                ];
            }, $data['coordinates']);
        }

        return $data;
    }
}
