<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Geofence;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\GeofenceUserService;

class GeofencesController extends Controller
{
    private GeofenceUserService $geofenceService;

    public function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {
            $this->geofenceService = new GeofenceUserService($this->user);

            return $next($request);
        });
    }

    public function index()
    {
        try {
            $this->checkException('geofences', 'view');

            return ['items' => [
                'geofences' => Geofence::userOwned($this->user)->get()
            ]];
        } catch (\Exception $e) {
            return ['items' => ['geofences' => []]];
        }
    }

    public function create()
    {
        if (!$this->user->perm('geofences', 'edit'))
            return ['status' => 0, 'perm' => 0];

        return ['status' => 1];
    }

    public function store()
    {
        $geofence = $this->geofenceService->create($this->data);

        return ['status' => 1, 'item' => $geofence];
    }

    public function update()
    {
        $model = Geofence::find($this->data['id']);

        $this->geofenceService->edit($model, $this->data);

        return ['status' => 1];
    }

    public function changeActive()
    {
        $validator = Validator::make($this->data, [
            'id' => 'required_without:group_id',
            'group_id' => 'required_without:id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $this->geofenceService->changeActive(
            $this->data['id'] ?? false,
            $this->data['group_id'] ?? false,
            $this->data['active'] ?? 0
        );

        return ['status' => 1];
    }

    public function destroy()
    {
        $id = array_key_exists('geofence_id', $this->data) ? $this->data['geofence_id'] : $this->data['id'];

        $item = Geofence::find($id);

        $this->geofenceService->remove($item);

        return ['status' => 1];
    }

    public function pointIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()]);

        if (is_null($geofences = $this->user->geofences))
            throw new ResourseNotFoundException(trans('front.geofences'));

        return response()->json([
            'status' => 1,
            'zones'  => getPointZones($geofences, $request->lat, $request->lng)
        ]);
    }
}
