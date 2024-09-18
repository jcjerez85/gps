<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Transformers\DeviceService\DeviceServiceFullTransformer;
use CustomFacades\ModalHelpers\ServiceModalHelper;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceService;
use FractalTransformer;

class ServicesController extends BaseController
{
    public function index($device_id)
    {
        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);

        $services = DeviceService::where('device_id', $device_id)->paginate(15);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::paginate($services, DeviceServiceFullTransformer::class)->toArray()
        ));
    }

    public function create($device_id)
    {
        return response()->json(ServiceModalHelper::createData($device_id));
    }

    public function store($device_id)
    {
        return response()->json(array_merge(
            ['status' => 1],
            ServiceModalHelper::create($device_id)));
    }

    public function edit($service_id)
    {
        $item = DeviceService::find($service_id);

        $this->checkException('devices', 'show', $item->device);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::item($item, DeviceServiceFullTransformer::class)->toArray(),
            ServiceModalHelper::createData($item->device->id)
        ));
    }

    public function update($service_id)
    {
        return response()->json(ServiceModalHelper::edit($service_id));
    }

    public function destroy($service_id) {
        return response()->json(ServiceModalHelper::destroy($service_id));
    }
}
