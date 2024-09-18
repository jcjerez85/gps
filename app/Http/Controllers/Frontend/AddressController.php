<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.3.12
 * Time: 17.44
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\GeoLocation;
use Validator;
use Tobuli\Exceptions\ValidationException;

class AddressController extends Controller
{
    public function get()
    {
        $data = request()->all();

        $this->validatePoint($data);

        if (settings('plugins.geofence_over_address.status') && $this->user)
            return GeoLocation::locateGeofence($this->user, $data['lat'], $data['lon']);

        return GeoLocation::resolveAddress($data['lat'], $data['lon']);
    }

    public function search()
    {
        try {
            $location = GeoLocation::byAddress(request()->input('q'));

            if ($location)
                return ['status' => 1, 'location' => $location->toArray()];

            return ['status' => 0, 'error' => trans('front.nothing_found_request')];
        } catch(\Exception $e) {
            return ['status' => 0, 'error' => $e->getMessage()];
        }
    }

    public function autocomplete()
    {
        try {
            $locations = GeoLocation::listByAddress(request()->input('q'));
        } catch (\Exception $e) {
            $locations = [];
        }

        return response()->json(
            array_map(function($location){ return $location->toArray();}, $locations)
        );
    }

    public function map()
    {
        $data = request()->all();

        $validator = Validator::make($data, [
            'lat' => 'lat',
            'lng' => 'lng',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $lat = $data['lat'] ?? null;
        $lng = $data['lng'] ?? null;

        $data['coords'] = $lat && $lng ? '['.$lat.', '.$lng.']' : null;

        return view('front::Addresses.index')->with($data);
    }

    public function reverse()
    {
        $data = request()->all();

        $validator = Validator::make($data, [
            'lat' => 'lat',
            'lng' => 'lng',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        try {
            $location = GeoLocation::byCoordinates($data['lat'], $data['lng']);

            if ($location)
                return ['status' => 1, 'data' => $location->toArray()];

            return ['status' => 0, 'error' => trans('front.nothing_found_request')];
        } catch(\Exception $e) {
            return ['status' => 0, 'error' => $e->getMessage()];
        }
    }

    private function validatePoint(array $data)
    {
        $validator = Validator::make($data, [
            'lat' => 'required|lat',
            'lon' => 'required|lng',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }
    }
}
