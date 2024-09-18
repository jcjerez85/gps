<?php namespace App\Http\Controllers\Api;

use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use App\Transformers\ApiV1\DeviceFullTransformer;
use CustomFacades\ModalHelpers\DeviceModalHelper;
use CustomFacades\Repositories\DeviceGroupRepo;
use CustomFacades\Repositories\SmsEventQueueRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Server;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tobuli\Exceptions\ValidationException;
use Formatter;
use Tobuli\Services\FcmService;
use Tobuli\Services\FractalSerializers\WithoutDataArraySerializer;
use Tobuli\Services\FractalTransformerService;
use Validator;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\MiddlewareNameResolver;
use Illuminate\Routing\SortedMiddleware;
use FractalTransformer;

class ApiController extends Controller
{
    protected $transformerService;

    public function __construct(FractalTransformerService $transformerService)
    {
        parent::__construct();

        $this->transformerService = $transformerService->setSerializer(WithoutDataArraySerializer::class);
    }

    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);

        if (isPublic()) {
            if ($user = \CustomFacades\RemoteUser::getByCredencials($this->data['email'], $this->data['password'])) {
                return [
                    'status' => 1,
                    'user_api_hash' => $user->api_hash,
                    'permissions' => $user->getPermissions(),
                ];
            }
        } else {
            if (Auth::attempt(['email' => $this->data['email'], 'password' => $this->data['password']])) {

                if ( ! Auth::User()->active)
                {
                    Auth::logout();
                    return response()->json(['status' => 0, 'message' => trans('front.login_suspended')], 401);
                }

                if (Auth::User()->isExpired())
                {
                    Auth::logout();
                    return response()->json(['status' => 0, 'message' => trans('front.subscription_expired')], 401);
                }

                if (empty(Auth::User()->api_hash)) {
                    while (!empty(UserRepo::findWhere(['api_hash' => $hash = Hash::make(Auth::User()->email . ':' . $this->data['password'])]))) ;
                    Auth::User()->api_hash = $hash;
                    Auth::User()->save();
                }

                return [
                    'status'        => 1,
                    'user_api_hash' => Auth::User()->api_hash,
                    'permissions'   => Auth::User()->getPermissions()
                ];
            }
        }

        return response()->json(['status' => 0, 'message' => trans('front.login_failed')], 401);
    }

    public function getSmsEvents()
    {
        UserRepo::updateWhere(['id' => $this->user->id], ['sms_gateway_app_date' => date('Y-m-d H:i:s')]);
        $items = SmsEventQueueRepo::getWhereSelect(['user_id' => $this->user->id], ['id', 'phone', 'message'], 'created_at')->toArray();


        if (!empty($items))
            SmsEventQueueRepo::deleteWhereIn(Arr::pluck($items, 'id'));

        return [
            'status' => 1,
            'items' => $items
        ];
    }

    #
    # Devices
    #

    public function getDevices()
    {
        if (!$this->user->perm('devices', 'view')) {
            return [];
        }

        Server::setMemoryLimit(config('server.device_memory_limit'));

        $device_groups = DeviceGroupRepo::getWhere(['user_id' => $this->user->id])
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        $grouped = [];

        $query = $this->user->devices()
            ->with(['users'])
            ->search(request()->get('s'))
            ->filter(request()->all());

        if (request()->has('page') || request()->has('limit')) {
            $page  = request()->get('page', 1);
            $limit = request()->get('limit', 100);
            $limit = max(1, $limit);

            $devices = $query->forPage($page, $limit)->get();

            $this->groupDevices($grouped, $device_groups, $devices);
        } else {
            $query->chunk(500, function ($devices) use (&$grouped, $device_groups) {
                $this->groupDevices($grouped, $device_groups, $devices);
            });
        }

        return array_values($grouped);
    }

    protected function groupDevices(&$grouped, $device_groups, $devices)
    {
        DeviceFullTransformer::loadRelations($devices);

        foreach ($devices as $device) {
            $group_id = empty($device->pivot->group_id) ? 0 : $device->pivot->group_id;
            $group_id = empty($device_groups[$group_id]) ? 0 : $group_id;

            if (!isset($grouped[$group_id])) {
                $grouped[$group_id] = [
                    'id' => $group_id,
                    'title' => $device_groups[$group_id],
                    'items' => []
                ];
            }

            $grouped[$group_id]['items'][] = $this->transformerService->item($device, DeviceFullTransformer::class)->toArray();
        }
    }

    public function getDevicesJson()
    {
        $data = DeviceModalHelper::itemsJson();

        return $data;
    }

    public function getUserData()
    {
        $dStart = new \DateTime(date('Y-m-d H:i:s'));
        $dEnd = new \DateTime($this->user->subscription_expiration);
        $dDiff = $dStart->diff($dEnd);
        $days_left = $dDiff->days;

        $plan = isset($this->user->billing_plan->title)
            ? $this->user->billing_plan->title
            : trans('admin.group_' . $this->user->group_id);

        return [
            'email' => $this->user->email,
            'expiration_date' => $this->user->subscription_expiration != '0000-00-00 00:00:00'
                ? Formatter::time()->human($this->user->subscription_expiration)
                : NULL,
            'days_left' => $this->user->subscription_expiration != '0000-00-00 00:00:00' ? $days_left : NULL,
            'plan' => $plan,
            'devices_limit' => intval($this->user->devices_limit),
            'group_id'      => $this->user->group_id,
            'permissions'   => $this->user->getPermissions()
        ];
    }

    public function setDeviceExpiration()
    {
        if (!isAdmin())
            return response()->json(['status' => 0, 'error' => trans('front.dont_have_permission')], 403);

        $validator = Validator::make(request()->all(), [
            'imei' => 'required',
            'expiration_date' => 'required|date',
        ]);

        if ($validator->fails())
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 400);

        $device = \Tobuli\Entities\Device::where('imei', request()->get('imei'))->first();

        if (!$device)
            return response()->json(['status' => 0, 'errors' => ['imei' => dontExist('global.device')]], 400);

        if ( ! $this->user->can('edit', $device, 'expiration_date'))
            throw new PermissionException();

        $device->expiration_date = request()->get('expiration_date');
        $device->save();

        return response()->json(['status' => 1], 200);
    }

    public function enableDeviceActive()
    {
        $validator = Validator::make(request()->all(), ['id' => 'required']);

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        $device = \Tobuli\Entities\Device::find(request('id'));

        $this->checkException('devices', 'enable', $device);

        if (!$device->active) {
            $device->active = true;
            $device->Save();
        }

        return response()->json(['status' => 1], 200);
    }

    public function disableDeviceActive()
    {
        $validator = Validator::make(request()->all(), ['id' => 'required']);

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        $device = \Tobuli\Entities\Device::find(request('id'));

        $this->checkException('devices', 'disable', $device);

        if ($device->active) {
            $device->active = false;
            $device->Save();
        }

        return response()->json(['status' => 1], 200);
    }

    public function geoAddress()
    {
        if (empty($this->data['lat']) || empty($this->data['lon']))
            return '-';

        return getGeoAddress($this->data['lat'], $this->data['lon']);
    }

    public function setFcmToken(FcmService $fcmService)
    {
        $validator = Validator::make(request()->all(), ['token' => 'required']);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $fcmService->setFcmToken($this->user, $this->data['token']);

        return response()->json(['status' => 1]);
    }

    public function getServicesKeys()
    {
        $services = [];

        $services['maps']['google']['key'] = settings('main_settings.google_maps_key');

        return response()->json(['status' => 1, 'items' => $services], 200);
    }

    public function __call($name, $arguments)
    {
        list($class, $method) = explode('#', $name);

        try {
            $controller = App::make("App\Http\Controllers\Frontend\\" . $class);
            $response = $this->runController($controller, $method, $arguments);
        } catch (\ReflectionException $e) {
            return response()->json(['status' => 0, 'message' => 'Method does not exist!'], 500);
        }

        if ( ! is_array($response))
            return $response;

        if (!array_key_exists('status', $response))
            $response['status'] = 1;

        $status_code = 200;
        if ($response['status'] == 0)
            $status_code = 400;

        if (array_key_exists('perm', $response))
            $status_code = 403;

        return response()->json($response, $status_code);
    }

    private function runController($controller, $method, $arguments)
    {
        $middleware = $this->getControllerMiddleware($controller, $method);

        $middleware = $this->sortMiddleware($middleware);

        return (new Pipeline(app()))
            ->send(request())
            ->through($middleware)
            ->then(function ($request) use ($controller, $method, $arguments) {
                return app()
                    ->call([$controller, $method], $arguments);
            });
    }

    private function getControllerMiddleware($controller, $method)
    {
        return collect($this->controllerMidlleware($controller, $method))
            ->map(function ($name) {
                return (array)MiddlewareNameResolver::resolve(
                    $name,
                    app('router')->getMiddleware(),
                    app('router')->getMiddlewareGroups());
            })->flatten();
    }

    private function controllerMidlleware($controller, $method)
    {
        return (new ControllerDispatcher(app()))
            ->getMiddleware($controller, $method);
    }

    private function sortMiddleware($middleware)
    {
        return (new SortedMiddleware(app('router')->middlewarePriority, $middleware))
            ->all();
    }
}
