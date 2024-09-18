<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceRouteType;
use Facades\Tobuli\Validation\DeviceRoutesTypeFormValidator;
use Formatter;

class DeviceRoutesTypeController extends Controller
{
    public function show($device_id)
    {
        $device = Device::find($device_id);

        $this->checkException('devices', 'show', $device);

        $routes = DeviceRouteType::query()
            ->where('device_id', $device->id)
            ->orderBy('started_at')
            ->paginate();

        return view('front::DeviceRoutesType.show', [
            'device' => $device,
            'routes' => $routes
        ]);
    }

    public function table($device_id)
    {
        $device = Device::find($device_id);

        $this->checkException('devices', 'show', $device);

        $routes = DeviceRouteType::query()
            ->where('device_id', $device->id)
            ->orderBy('started_at')
            ->paginate();

        return view('front::DeviceRoutesType.table', [
            'device' => $device,
            'routes' => $routes
        ]);
    }

    public function create($device_id)
    {
        $device = Device::find($device_id);

        $this->checkException('devices', 'show', $device);

        return view('front::DeviceRoutesType.create', [
            'device' => $device,
            'types' => DeviceRouteType::types()
        ]);
    }

    public function store($device_id)
    {
        $device = Device::find($device_id);

        $this->checkException('devices', 'show', $device);

        DeviceRoutesTypeFormValidator::validate('create', $this->data);

        DeviceRouteType::create([
            'user_id'    => $this->user->id,
            'device_id'  => $device->id,
            'type'       => $this->data['type'],
            'started_at' => Formatter::time()->reverse( $this->data['started_at']),
            'ended_at'   => Formatter::time()->reverse( $this->data['ended_at']),
        ]);

        return response()->json(['status' => 1]);
    }

    public function edit($id)
    {
        $route = DeviceRouteType::find($id);

        $this->checkException('device_route_types', 'edit', $route);

        return view('front::DeviceRoutesType.edit', [
            'device'   => $route->device,
            'route'    => $route,
            'types'    => DeviceRouteType::types()
        ]);
    }

    public function update($id)
    {
        $route = DeviceRouteType::find($id);

        $this->checkException('device_route_types', 'update', $route);

        DeviceRoutesTypeFormValidator::validate('update', $this->data);

        $route->update([
            'type'       => $this->data['type'],
            'started_at' => Formatter::time()->reverse( $this->data['started_at']),
            'ended_at'   => Formatter::time()->reverse( $this->data['ended_at']),
        ]);

        return response()->json(['status' => 1]);
    }

    public function destroy($id)
    {
        $route = DeviceRouteType::find($id);

        $this->checkException('device_route_types', 'remove', $route);

        $route->delete();

        return response()->json(['status' => 1]);
    }
}
