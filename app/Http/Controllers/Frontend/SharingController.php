<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use CustomFacades\Repositories\SharingRepo;
use CustomFacades\Validators\SharingFormValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\Device;
use Tobuli\Entities\Sharing;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\SharingService;
use Formatter;
use Validator;

class SharingController extends Controller
{
    private $sharingService;

    public function __construct(SharingService $sharingService)
    {
        parent::__construct();

        $this->sharingService = $sharingService;
    }

    public function index()
    {
        $this->checkException('sharing', 'view');

        $data = Sharing::where('user_id', $this->user->id)
            ->filter($this->data)
            ->paginate(10);

        $devices = groupDevices($this->user->devices, $this->user);
        $durationTimes = config('tobuli.object_online_timeouts');

        $selectedDevices = Arr::get($this->data, 'devices_id', []);

        $selectedDevices = is_array($selectedDevices) ? $selectedDevices : [$selectedDevices];

        return view('front::Sharing.index')
            ->with(compact('data', 'devices', 'durationTimes', 'selectedDevices'));
    }

    public function table()
    {
        $this->checkException('sharing', 'view');

        $data = Sharing::where('user_id', $this->user->id)
            ->filter($this->data)
            ->paginate(10);

        $selectedDevices = Arr::get($this->data, 'devices_id', []);

        $selectedDevices = is_array($selectedDevices) ? $selectedDevices : [$selectedDevices];

        return view('front::Sharing.table')
            ->with(compact('data', 'selectedDevices'));
    }

    public function edit($sharingId)
    {
        $sharing = SharingRepo::find($sharingId);

        $this->checkException('sharing', 'edit', $sharing);

        $devices = groupDevices($this->user->devices, $this->user);

        return view('front::Sharing.edit')->with(compact('sharing', 'devices'));
    }

    public function update($sharingId)
    {
        $sharing = SharingRepo::find($sharingId);

        $this->checkException('sharing', 'update', $sharing);

        SharingFormValidator::validate('update', $this->data);

        if (Arr::get($this->data, 'enable_expiration_date'))
            $this->data['expiration_date'] = Formatter::time()->reverse($this->data['expiration_date']);
        else
            $this->data['expiration_date'] = null;

        $this->sharingService->update($sharing, $this->data);

        $devices = Device::whereIn('id', $this->data['devices'])->filterUserAbility($this->user);

        $this->sharingService->syncDevices($sharing, $devices);

        return ['status' => 1];
    }

    public function create()
    {
        $this->checkException('sharing', 'create');

        $devices = groupDevices($this->user->devices, $this->user);

        return view('front::Sharing.create')->with(compact('devices'));
    }

    public function store()
    {
        $this->checkException('sharing', 'store');

        SharingFormValidator::validate('create', $this->data);

        $this->normalize($this->data);

        $sharing = $this->sharingService->create($this->user->id, $this->data);

        $devices = Device::whereIn('id', $this->data['devices'])->filterUserAbility($this->user);

        $this->sharingService->syncDevices($sharing, $devices);

        return ['status' => 1];
    }

    public function doDestroy($sharingId)
    {
        $sharing = SharingRepo::find($sharingId);

        $this->checkException('sharing', 'remove', $sharing);

        return view('front::Sharing.destroy')->with(['id' => $sharingId]);
    }

    public function destroy()
    {
        $id = request()->get('id');

        $sharing = SharingRepo::find($id);

        $this->checkException('sharing', 'remove', $sharing);

        $this->sharingService->remove($sharing);

        return ['status' => 1];
    }

    public function createInstant()
    {
        $deviceId = request()->get('device_id') ?? null;

        $device = Device::find($deviceId);

        $this->checkException('sharing', 'create');
        $this->checkException('devices', 'own', $device);

        $sharing = $this->sharingService->create($this->user->id);
        $this->sharingService->syncDevices($sharing, $device);

        return ['status' => 1];
    }

    public function sendForm()
    {
        $this->checkException('sharing', 'create');

        $devices = groupDevices($this->user->devices, $this->user);
        $durationTimes = Config::get('tobuli.object_online_timeouts');

        $selectedDevices = [];

        if (isset($this->data['device_id'])) {
            $selectedDevices['device_id'] = $this->data['device_id'];
        }

        return view('front::Sharing.send')->with(compact('devices', 'durationTimes', 'selectedDevices'));
    }

    public function send()
    {
        $this->checkException('sharing', 'create');

        SharingFormValidator::validate('send', $this->data);

        $this->normalize($this->data);
        $this->validateSendMethods($this->data);

        $sharing = $this->sharingService->create($this->user->id, $this->data);

        $devices = Device::whereIn('id', $this->data['devices'])->filterUserAbility($this->user);

        $this->sharingService->syncDevices($sharing, $devices);

        if (!empty($this->data['sms'])) {
            $this->sharingService->sendSms($sharing, $this->data['sms']);
        }

        if (!empty($this->data['email'])) {
            $this->sharingService->sendEmail($sharing, $this->data['email']);
        }

        return ['status' => 1];
    }

    private function normalize( & $data)
    {
        switch($data['expiration_by']) {
            case 'date':
                $data['expiration_date'] = Formatter::time()->reverse($data['expiration_date']);

                break;
            case 'duration':
                $data['expiration_date'] = Carbon::now()->addMinutes($data['duration']);

                break;
            default:
                $data['expiration_date'] = null;
        }

        if ( ! empty($data['sms'])) {
            $data['sms'] = semicol_explode($data['sms']);
        }

        if ( ! empty($data['email'])) {
            $data['email'] = semicol_explode($data['email']);
        }
    }

    private function validateSendMethods($data)
    {
        $validator = Validator::make(
            $data,
            [
                'sms' => 'array',
                'email' => 'array',
            ]
        );

        if ($validator->fails()) {
            $errors = [];

            foreach($validator->errors()->keys() as $key) {
                $errors[$key] = $validator->errors()->first($key);
            }

            throw new ValidationException($errors);
        }
    }
}
