<?php

namespace App\Http\Controllers\Admin;

use CustomFacades\Repositories\UserRepo;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\DeviceService;

class ObjectsUsersController extends BaseController
{
    /**
     * @var DeviceService
     */
    protected $deviceService;

    public function __construct(DeviceService $service)
    {
        parent::__construct();

        $this->deviceService = $service;
    }

    public function assignForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        return view('admin::Objects.assign', [
            'users' => UserRepo::getUsers($this->user),
            'device_id' => $request->get('id'),
        ]);
    }

    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|array',
            'user_id' => 'required|array',
            'action' => 'required|in:attach,detach',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $users = User::whereIn('users.id', $request->get('user_id'))
            ->get()
            ->filter(function($user){
                return $this->user->can('edit', $user);
            });

        $devices = $this->user->accessibleDevices()
            ->whereIn('devices.id', $request->get('device_id'))
            ->with(['users' => function($query) use ($users) {
                $query->whereIn('users.id', $users->pluck('id')->all());
            }])
            ->get();

        switch ($request->get('action')) {
            case 'attach':
                foreach ($devices as $device) {
                    foreach($users as $user) {
                        if ($device->users->find($user->id))
                            continue;

                        $this->deviceService->addUser($device, $user);
                    }
                }
                break;
            case 'detach':
                foreach ($devices as $device) {
                    foreach($device->users as $user) {
                        $this->deviceService->removeUser($device, $user);
                    }
                }
                break;
        }

        return Response::json(['status' => 1]);
    }
}
