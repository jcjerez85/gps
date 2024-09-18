<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Transformers\Geofence\GeofenceMapTransformer;
use FractalTransformer;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\GeofenceGroup;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\GeofenceUserService;

class GeofencesController extends Controller
{
    private GeofenceUserService $geofenceService;

    protected function afterAuth($user)
    {
        $this->geofenceService = new GeofenceUserService($this->user);
    }

    public function index()
    {
        $this->checkException('geofences', 'view');

        $items = Geofence::userOwned($this->user)->paginate(500);

        return response()->json(
            FractalTransformer::paginate($items, GeofenceMapTransformer::class)->toArray()
        );
    }

    public function create()
    {
        $this->checkException('geofences', 'store');

        $data = $this->getFormData();

        return view('front::Geofences.create')->with($data);
    }

    public function edit(int $id)
    {
        $item = Geofence::find($id);

        $this->checkException('geofences', 'edit', $item);

        $data = $this->getFormData();

        if (settings('plugins.geofences_speed_limit.status') && $item->speed_limit !== null) {
            $item->speed_limit = round(\Formatter::speed()->convert($item->speed_limit));
        }

        $data['item'] = $item;

        return view('front::Geofences.edit')->with($data);
    }

    private function getFormData()
    {
        $geofenceTypes = ['polygon' => trans('front.polygon'), 'circle' => trans('front.circle')];

        $geofenceGroups = GeofenceGroup::userOwned($this->user)
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        $data = compact('geofenceTypes', 'geofenceGroups');

        if (settings('plugins.moving_geofence.status')) {
            $data['devices'] = $this->user->devices()->get();
        }

        return $data;
    }

    public function store()
    {
        $geofence = $this->geofenceService->create($this->data);

        return ['status' => 1] + FractalTransformer::item($geofence, GeofenceMapTransformer::class)->toArray();
    }

    public function update()
    {
        $geofence = Geofence::find($this->data['id']);

        $this->geofenceService->edit($geofence, $this->data);

        return ['status' => 1] + FractalTransformer::item($geofence, GeofenceMapTransformer::class)->toArray();
    }

    public function destroy()
    {
        $id = array_key_exists('geofence_id', $this->data) ? $this->data['geofence_id'] : $this->data['id'];

        $item = Geofence::find($id);

        $this->geofenceService->remove($item);

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
}
