<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use Tobuli\Entities\Device;
use Tobuli\Entities\DevicePlan;

class DevicePlansController extends Controller
{
    public function __construct()
    {
        if (! settings('main_settings.enable_device_plans') ?? false) {
            throw new PermissionException();
         }

         parent::__construct();
    }

    public function index($device_id = null)
    {
        $devices = $this->user->devices;

        $device = $this->user->devices()->find($device_id);

        if (empty($device) && $devices)
            $device = $devices->first();

        $plans = $device
            ? DevicePlan::active()->forDevice($device)->orderBy('price')->get()
            : DevicePlan::active()->orderBy('price')->get();

        return view('front::DevicePlans.index', [
            'plans'     => $plans,
            'devices'   => $devices->pluck('name', 'id'),
            'device_id' => $device->id ?? null
        ]);
    }

    public function plans($device_id)
    {
        $device = $this->user->devices()->find($device_id);

        $this->checkException('devices', 'view', $device);

        DevicePlan::active()->forDevice($device)->orderBy('price')->get();

        return view('front::DevicePlans.plans', [
            'plans'     => DevicePlan::active()->forDevice($device)->orderBy('price')->get(),
            'device_id' => $device->id
        ]);
    }
}
