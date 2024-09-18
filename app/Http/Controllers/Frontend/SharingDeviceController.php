<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use CustomFacades\Repositories\SharingRepo;
use CustomFacades\Validators\SharingDeviceFormValidator;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\Device;
use Tobuli\Entities\Sharing;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\SharingService;

class SharingDeviceController extends Controller
{
    private $sharingService;

    public function __construct(SharingService $sharingService)
    {
        parent::__construct();

        $this->sharingService = $sharingService;
    }

    //@TODO: not used
    public function index($deviceId)
    {
        $this->checkException('sharing', 'view');

        //$data = SharingRepo::getUserSharingByDevice($this->user->id, $deviceId)->paginate(10);
        $data = Sharing::where('user_id', $this->user->id)
            ->filter([
                'devices_id' => $deviceId,
            ])
            ->get()
            ->paginate(10);

        $devices = groupDevices($this->user->devices, $this->user);
        $durationTimes = Config::get('tobuli.object_online_timeouts');

        $selectedDevices = [];
        $selectedDevices[$deviceId] = $deviceId;

        return view('front::Sharing.index')
            ->with(compact('data', 'devices', 'durationTimes', 'selectedDevices', 'deviceId'));
    }

    //@TODO: not used
    public function table($deviceId)
    {
        $this->checkException('sharing', 'view');

        $data = SharingRepo::getUserSharingByDevice($this->user->id, $deviceId)->paginate(10);

        return view('front::Sharing.device.table')
            ->with(compact('data', 'deviceId'));
    }

    public function doDestroy($deviceId, $sharingId)
    {
        $sharing = SharingRepo::find($sharingId);

        $device = Device::find($deviceId);

        $this->checkException('sharing', 'edit', $sharing);
        $this->checkException('devices', 'own', $device);

        return view('front::Sharing.device.destroy')
            ->with([
                'sharing_id' => $sharingId,
                'device_id'  => $deviceId,
            ]);
    }

    public function destroy($deviceId, $sharingId)
    {
        $sharing = SharingRepo::find($sharingId);
        $device = Device::find($deviceId);

        $this->checkException('sharing', 'edit', $sharing);
        $this->checkException('devices', 'own', $device);

        $this->sharingService->removeDevices($sharing, $device);

        return ['status' => 1];
    }

    public function addToSharing($deviceId)
    {
        $device = Device::find($deviceId);
        $this->checkException('devices', 'own', $device);

        $sharings = Sharing::where('user_id', $this->user->id)
            ->withoutDevices([$deviceId])
            ->get()->pluck('name', 'id');

        return view('front::Sharing.device.to_sharing')
            ->with(compact('sharings', 'deviceId'));
    }

    public function saveToSharing($deviceId)
    {
        $sharingId = request()->get('sharing_id') ?? null;

        $device = Device::find($deviceId);
        $sharing = SharingRepo::find($sharingId);

        $this->checkException('sharing', 'edit', $sharing);
        $this->checkException('devices', 'own', $device);

        $this->sharingService->addDevices($sharing, $device);

        return ['status' => 1];
    }
}
