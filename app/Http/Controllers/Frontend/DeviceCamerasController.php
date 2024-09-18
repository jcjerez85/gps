<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\Repositories\DeviceCameraRepo;
use Tobuli\Entities\DeviceCamera;
use Tobuli\Exceptions\ValidationException;
use App\Exceptions\ResourseNotFoundException;
use Validator;

class DeviceCamerasController extends Controller
{
    public function index($device_id)
    {
        $this->checkException('device_camera', 'view');

        $data = DeviceCameraRepo::searchAndPaginate(['filter' => ['device_id' => $device_id]], 'id', 'desc', 10);

        if (!$this->api) {
            $data = [
                'device_cameras' => $data,
                'device_id' => $device_id
            ];
        }

        return !$this->api ? view('front::DeviceMedia.partials.cameras.index')->with($data) : $data;
    }

    public function create($device_id)
    {
        $this->checkException('device_camera', 'create');

        return view('front::DeviceMedia.partials.cameras.create', [
            'device_id' => $device_id,
        ]);
    }

    public function store()
    {
        $this->checkException('device_camera', 'store');
        $validator = Validator::make($this->data,
            [
                'name' => 'required|alpha_dash',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $device_camera = new DeviceCamera($this->data);
        $device_camera->save();

        return ['status' => 1];
    }

    public function edit($id)
    {
        $item = DeviceCameraRepo::find($id);

        $this->checkException('device_camera', 'edit', $item);

        return view('front::DeviceMedia.partials.cameras.edit')->with([
            'item' => $item,
        ]);
    }

    public function update()
    {
        $data = $this->data;
        $device_camera = DeviceCameraRepo::find($data['id']);

        $this->checkException('device_camera', 'update', $device_camera);

        $validator = Validator::make($data,
            [
                'name' => 'required|alpha_dash',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }


        $device_camera->fill($data);
        $device_camera->save();

        return ['status' => 1];
    }

    public function doDestroy($id)
    {
        $item = DeviceCameraRepo::find($id);

        $this->checkException('device_camera', 'remove', $item);

        return view('front::DeviceMedia.partials.cameras.destroy')->with(compact('item'));
    }

    public function destroy($id)
    {
        $item = DeviceCameraRepo::find($id);

        $this->checkException('device_camera', 'destroy', $item);

        DeviceCameraRepo::delete($id);

        return ['status' => 1];
    }
}
