<?php

namespace Tobuli\Services;

use CustomFacades\Validators\PoiFormValidator;
use Illuminate\Support\Arr;
use Tobuli\Entities\Poi AS Poi;


class PoiService extends ModelService
{
    public function __construct()
    {
        $this->setDefaults([
            'active' => true,
        ]);

        $this->setValidationRulesStore(
            PoiFormValidator::getFacadeRoot()->rules['create']
        );

        $this->setValidationRulesUpdate(
            PoiFormValidator::getFacadeRoot()->rules['update']
        );
    }

    public function store(array $data)
    {
        return Poi::create($data);
    }

    public function update($poi, array $data)
    {
        return $poi->update($data);
    }

    public function delete($poi)
    {
        return $poi->delete();
    }

    protected function normalize(array $data)
    {
        /*
         * For coordinates validation
         */
        if (array_key_exists('coordinates', $data)) {
            $lat = null;
            $lng = null;

            if (is_array($data['coordinates'])) {
                $lat = Arr::get($data['coordinates'], 'lat');
                $lng = Arr::get($data['coordinates'], 'lng');
            }

            if (is_string($data['coordinates']) && $cords = json_decode($data['coordinates'])) {
                $lat = $cords->lat;
                $lng = $cords->lng;
            }

            $data['coordinates'] = [
                'lat' => $lat,
                'lng' => $lng,
            ];
        }

        return $data;
    }
}
