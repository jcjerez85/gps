<?php namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\PermissionException;
use App\Transformers\Device\DeviceFullTransformer;
use App\Transformers\Device\DeviceListTransformer;
use App\Transformers\Device\DeviceLookupTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Device;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\DeviceService;

use FractalTransformer;

class DevicesController extends BaseController
{
    /**
     * @var DeviceService
     */
    protected $service;

    public function __construct(DeviceService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function index(Request $request) {
        $this->checkException('devices', 'view');

        $query = $this->user
            ->accessibleDevices()
            ->filter($request->all())
            ->search($request->get('s'))
            ->includes($request->get('includes'));

        $devices = $query->paginate($request->get('limit', 50));

        $devices->appends($request->except('user_api_hash'));

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::paginate($devices, DeviceLookupTransformer::class)->toArray()
        ));
    }

    public function get(Request $request, $device_id) {
        $device = $this->user
            ->accessibleDevices()
            ->find($device_id);

        $this->checkException('devices', 'show', $device);

        return Response::json(array_merge(
            ['status' => 1],
            FractalTransformer::item($device, DeviceFullTransformer::class)->toArray()
        ));
    }

    public function addUser(Request $request, $identifier)
    {
        $device = Device::whereIdOrImei($identifier)->first();

        $this->checkException('devices', 'show', $device);

        $user = $this->getUser($request);

        $this->service->addUser($device, $user);

        return Response::json([
            'status' => 1
        ]);
    }

    public function removeUser(Request $request, $identifier)
    {
        $device = Device::whereIdOrImei($identifier)->first();

        $this->checkException('devices', 'show', $device);

        $user = $this->getUser($request);

        $this->service->removeUser($device, $user);

        return Response::json([
            'status' => 1
        ]);
    }

    public function expiration(Request $request, $identifier) {
        $validator = Validator::make($request->all(), [
            'expiration_date' => 'required|date',
        ]);

        if ($validator->fails())
            throw new ValidationException( $validator->messages() );

        $device = Device::whereIdOrImei($identifier)->first();

        $this->checkException('devices', 'edit', $device);

        if ( ! $this->user->can('edit', $device, 'expiration_date'))
            throw new PermissionException();

        $device->update([
            'expiration_date' => $request->input('expiration_date')
        ]);

        return Response::json([
            'status' => 1
        ]);
    }

    public function setStatus(Request $request, $identifier)
    {
        $validator = Validator::make($request->all(), [
            'active' => 'required|in:0,1',
        ]);

        if ($validator->fails())
            throw new ValidationException( $validator->messages() );

        $device = Device::whereIdOrImei($identifier)->first();

        $this->checkException('devices', 'enable', $device);

        $device->active = $request->get('active');
        $device->save();

        return response()->json(['status' => 1], 200);
    }

    protected function getUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required_without:email|integer',
            'email'   => 'required_without:user_id|email',
        ]);

        if ($validator->fails())
            throw new ValidationException( $validator->messages() );

        if ($request->get('user_id'))
            $user = User::where('id', $request->get('user_id'))->first();
        else
            $user = User::where('email', $request->get('email'))->first();

        $this->checkException('users', 'show', $user);

        return $user;
    }

}
