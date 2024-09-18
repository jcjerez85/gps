<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ResourseNotFoundException;
use CustomFacades\Validators\AdminDeviceTypeImeiValidator;
use Illuminate\Http\Request;
use Tobuli\Entities\DeviceType;
use Tobuli\Entities\DeviceTypeImei;
use Tobuli\Exceptions\ValidationException;
use Illuminate\Support\Facades\Validator;
use Tobuli\Importers\DeviceTypeImei\DeviceTypeImeiImportManager;

class DeviceTypeImeiController extends BaseController
{
    public function index(Request $request)
    {
        $items = DeviceTypeImei::with('deviceType')
            ->filter($request->all())
            ->search($request->get('s'))
            ->paginate(15)
            ->setPath(route('admin.device_type_imei.table'));

        return view('admin::DeviceTypeImei.'.(request()->ajax() ? 'modal' : 'index'), [
            'items' => $items,
            'device_type_id' => $request->get('device_type_id'),
        ]);
    }

    public function table(Request $request)
    {
        $items = DeviceTypeImei::with('deviceType')
            ->filter($request->all())
            ->search($request->get('s'))
            ->paginate(15);

        return view('admin::DeviceTypeImei.table', [
            'items' => $items,
            'device_type_id' => $request->get('device_type_id'),
        ]);
    }

    public function create()
    {
        return view('admin::DeviceTypeImei.create', [
            'device_types' => DeviceType::all(),
        ]);
    }

    public function store()
    {
        AdminDeviceTypeImeiValidator::validate('create', $this->data);

        $deviceTypeImei = DeviceTypeImei::create($this->data);

        return response()->json(['status' => 1]);
    }

    public function edit($id)
    {
        $deviceTypeImei = DeviceTypeImei::find($id);

        if (! $deviceTypeImei) {
            throw new ResourseNotFoundException(trans('front.device_type_imei'));
        }

        return view('admin::DeviceTypeImei.edit', [
            'item' => $deviceTypeImei,
            'device_types' => DeviceType::all(),
        ]);
    }

    public function update()
    {
        $deviceTypeImei = DeviceTypeImei::find($this->data['id']);

        AdminDeviceTypeImeiValidator::validate('update', $this->data, $deviceTypeImei->id);

        $deviceTypeImei->update($this->data);

        return response()->json(['status' => 1]);
    }

    public function destroy()
    {
        $deviceTypeImei = DeviceTypeImei::find($this->data['id'] ?? null);

        if (!$deviceTypeImei) {
            throw new ResourseNotFoundException(trans('front.device_type_imei'));
        }

        $deviceTypeImei->delete();

        return response()->json(['status' => 1]);
    }

    public function importForm()
    {
        return view('admin::DeviceTypeImei.import', [
            'device_types' => DeviceType::all(),
        ]);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_type_id' => 'required|exists:device_types,id',
            'file'           => 'required|file',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        $manager = new DeviceTypeImeiImportManager();
        $manager->import($request->file('file'), [
            'device_type_id' => $request->get('device_type_id')
        ]);

        return response()->json([
            'status' => 1,
            'message' => trans('front.successfully_saved'),
        ]);
    }
}
