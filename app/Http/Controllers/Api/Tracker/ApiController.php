<?php

namespace App\Http\Controllers\Api\Tracker;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Device;
use Tobuli\Entities\TrackerPort;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\Tracker;
use Tobuli\Services\FcmService;

class ApiController extends Controller
{
    /**
     * @var Device
     */
    protected $deviceInstance;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->deviceInstance = app()->make(Device::class);

            return $next($request);
        });
    }

    public function login()
    {
        $url = null;

        if (empty($this->deviceInstance->protocol) || $this->deviceInstance->protocol == 'osmand') {
            $port = TrackerPort::active()->where('name', 'osmand')->value('port');
            $url = (new Tracker())->getUrl(true) . ($port ? ":$port" : "");
        }

        return response()->json([
            'success' => true,
            'data' => [
                'url'       => $url,
                'device_id' => $this->deviceInstance->id,
                'channel'   => md5('device_' . $this->deviceInstance->id),
            ]
        ]);
    }

    public function setFcmToken(FcmService $fcmService)
    {
        $input = request()->all();

        $validator = Validator::make($input, ['token' => 'required']);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $fcmService->setFcmToken($this->deviceInstance, $input['token']);

        return response()->json(['status' => 1, 'data' => $this->deviceInstance->fcmTokens()]);
    }
}