<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\PermissionException;
use App\Exceptions\ResourseNotFoundException;
use CustomFacades\Validators\AdminDevicePlanValidator;
use Tobuli\Entities\DevicePlan;
use Tobuli\Entities\DeviceType;

class DevicePlanController extends BaseController
{
    public function index()
    {
        $items = DevicePlan::paginate(15);

        return view('admin::DevicePlans.'.(request()->ajax() ? 'table' : 'index'), [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('admin::DevicePlans.create', [
            'durationTypes' => DevicePlan::getDurationTypes(),
            'deviceTypes'   => DeviceType::all(),
        ]);
    }

    public function store()
    {
        AdminDevicePlanValidator::validate('create', $this->data);
        $item = DevicePlan::create($this->data);

        if (array_key_exists('device_types', $this->data)) {
            $types = empty($this->data['device_types']) ? [] : $this->data['device_types'];
            $item->deviceTypes()->sync($types);
        }

        return response()->json(['status' => 1]);
    }

    public function edit($id)
    {
        $item = DevicePlan::with('deviceTypes')->find($id);

        if (! $item) {
            throw new ResourseNotFoundException(trans('front.device_plan'));
        }

        return view('admin::DevicePlans.edit', [
            'item' => $item,
            'durationTypes' => DevicePlan::getDurationTypes(),
            'deviceTypes'   => DeviceType::all(),
        ]);
    }

    public function update()
    {
        AdminDevicePlanValidator::validate('update', $this->data);
        $item = DevicePlan::find($this->data['id']);
        $item->update($this->data);

        if (array_key_exists('device_types', $this->data)) {
            $types = empty($this->data['device_types']) ? [] : $this->data['device_types'];
            $item->deviceTypes()->sync($types);
        }

        return response()->json(['status' => 1]);
    }

    public function destroy()
    {
        $item = DevicePlan::find($this->data['id'] ?? null);

        if (! $item) {
            throw new ResourseNotFoundException(trans('front.device_plan'));
        }

        $item->delete();

        return response()->json(['status' => 1]);
    }

    public function toggleActive()
    {
        $status = settings('main_settings.enable_device_plans') ?? false;
        settings('main_settings.enable_device_plans', ! $status);

        return response()->json(['status' => 1]);
    }
}
