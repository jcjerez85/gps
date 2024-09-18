<?php namespace App\Http\Controllers\Frontend;

use App\Exceptions\PermissionException;
use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use App\Transformers\Device\DeviceMapTransformer;
use CustomFacades\ModalHelpers\DeviceModalHelper;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\DeviceImageValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Session;
use Tobuli\Entities\Device;
use Tobuli\Entities\Geofence;
use Tobuli\Exceptions\ValidationException;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Actions\GroupGeofenceInOut;
use Tobuli\History\DeviceHistory;
use Tobuli\Services\DeviceService;
use FractalTransformer;
use Tobuli\Services\FractalSerializers\WithoutDataArraySerializer;

class DevicesController extends Controller
{
    private $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        parent::__construct();
        $this->deviceService = $deviceService;
    }

    public function create()
    {
        $data = DeviceModalHelper::createData();
        return is_array($data) && !$this->api ? view('front::Devices.create')->with($data) : $data;
    }

    public function store()
    {
        if ($this->user->perm('custom_device_add', 'view'))
            throw new PermissionException();

        //dd("Entre");

        return DeviceModalHelper::create();
    }

    public function edit($id = NULL, $admin = FALSE) {
        $data = DeviceModalHelper::editData();

        //dd($data);

        $view = $data['item']->isBeacon() ? 'front::Beacons.edit' : 'front::Devices.edit';

        return is_array($data) && !$this->api
            ? view($view)->with(array_merge($data, ['admin' => $admin]))
            : $data;
    }

    public function update()
    {
        return DeviceModalHelper::edit();
    }

    public function resetAppUuid(int $id)
    {
        return DeviceModalHelper::resetAppUuid($id);
    }

    public function doResetAppUuid(int $id): RedirectResponse
    {
        $response = DeviceModalHelper::resetAppUuid($id);

        if ($response['status'] ?? 0) {
            Session::flash('messages', [trans('global.success')]);
        }

        return redirect()->back();
    }

    public function changeActive()
    {
        return DeviceModalHelper::changeActive();
    }

    public function destroy()
    {
        if (config('tobuli.object_delete_pass') && isAdmin() && request('password') != config('tobuli.object_delete_pass')) {
            return ['status' => 0, 'errors' => ['message' => trans('front.login_failed')]];
        }

        return DeviceModalHelper::destroy();
    }

    public function doDestroy($id)
    {
        return view('front::Devices.destroy', compact('id'));
    }

    public function detach() {
        return DeviceModalHelper::detach();
    }

    public function stopTime($device_id = NULL)
    {
        if (is_null($device_id))
            $device_id = request()->get('device_id');

        $device = DeviceRepo::getWithFirst(['traccar', 'users', 'sensors'], ['id' => $device_id]);

        $this->checkException('devices', 'show', $device);

        return ['time' => $device->stopDuration];
    }

    public function followMap($device_id)
    {
        $item = Device::find($device_id);

        $this->checkException('devices', 'show', $item);

        return view('front::Devices.follow_map', [
            'item' => FractalTransformer::setSerializer(WithoutDataArraySerializer::class)
                ->item($item, DeviceMapTransformer::class)->toArray()
        ]);
    }

    public function inGeofences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|integer',
        ]);

        $this->checkException('devices', 'own', $device = Device::find($request->device_id));

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()]);

        if (is_null($geofences = $this->user->geofences))
            throw new ResourseNotFoundException(trans('front.geofences'));

        return response()->json([
            'status' => 1,
            'zones'  => getPointZones($geofences, $device->lat, $device->lng),
        ]);
    }

    public function wasInGeofence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from'        => 'required|date',
            'to'          => 'required|date',
            'device_id'   => 'required|integer',
            'geofence_id' => 'required|integer',
        ]);

        $this->checkException('devices', 'own', $device = Device::find($request->device_id));

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()]);

        if (is_null($geofence = $this->user->geofences()->find($request->geofence_id)))
            throw new ResourseNotFoundException('front.geofence');

        $positions = $device->positions()
            ->where('time', '>', $request->from)
            ->where('time', '<', $request->to)
            ->get();

        if (is_null($positions))
            throw new ResourseNotFoundException('front.position');

        $min_away_by = INF;
        $closest_point = null;
        
        foreach ($positions as $position) {
            $point = [
                'latitude'  => $position->latitude,
                'longitude' => $position->longitude,
            ];

            $away_by = $geofence->pointAwayBy($point);

            if ($away_by == 0)
                return response()->json(['status' => 1, 'was_in' => true]);

            if ($away_by < $min_away_by) {
                $closest_point = $point;
                $min_away_by = $away_by;
            }
        }

        return response()->json([
            'status'        => 1,
            'was_in'        => false,
            'closest_point' => $closest_point,
        ]);
    }

    public function stayInGeofence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from'        => 'required|date',
            'to'          => 'required|date',
            'device_id'   => 'required|integer',
            'geofence_id' => 'required|integer',
        ]);

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()]);

        $device = Device::find($request->device_id);

        $this->checkException('devices', 'own', $device);

        $geofence = Geofence::find($request->geofence_id);

        $this->checkException('geofences', 'own', $geofence);

        $geofences = [$geofence];
        $history = new DeviceHistory($device);
        $history->setGeofences($geofences);
        $history->setRange($request->from, $request->to);
        $history->registerActions([
            Duration::class,
            GroupGeofenceInOut::class,
        ]);

        $result = $history->get();
        $total  = $result['groups']->merge();

        try {
            $duration = $total->stats()->human('duration');
            $seconds  = $total->stats()->get('duration')->get();
        } catch (\Exception $e) {
            $duration = null;
            $seconds  = 0;
        }

        return response()->json([
            'status'   => 1,
            'duration' => $duration,
            'seconds'  => $seconds,
        ]);

    }

    public function uploadImage($device_id)
    {
        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'update', $device);
        DeviceImageValidator::validate('upload', $this->data);

       $this->deviceService->saveImage($device, $this->data['image']);

        return ['status' => 1];
    }

    public function deleteImage($device_id)
    {
        $device = DeviceRepo::find($device_id);
        $this->checkException('devices', 'update', $device);

        $this->deviceService->deleteImage($device);

        return ['status' => 1];
    }
}
