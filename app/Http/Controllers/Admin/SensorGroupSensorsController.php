<?php namespace App\Http\Controllers\Admin;

use CustomFacades\Repositories\DeviceSensorRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use ModalHelpers\SensorModalHelper;
use Tobuli\Entities\SensorGroupSensor;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\SensorGroup\SensorGroupRepositoryInterface as SensorGroup;
use Tobuli\Validation\AdminSensorGroupFormValidator;

class SensorGroupSensorsController extends BaseController {

    public function index($id, $ajax = 0) {
        $sensors_arr = Config::get('tobuli.sensors');

        $items = SensorGroupSensor::where(['group_id' => $id])
            ->orderBy('name')
            ->get()
            ->filter(function ($item) use ($sensors_arr) {
                return isset($sensors_arr[$item->type]);
            })
            ->map(function ($item) use ($sensors_arr) {
                $item->type_title = $sensors_arr[$item->type];

                return $item;
            });

        return view('admin::SensorGroupSensors.'.($ajax ? 'table' : 'index'))->with(compact('items', 'id'));
    }

    public function create($id, SensorModalHelper $sensorModalHelper) {
        $data = array_merge($sensorModalHelper->createData(null), [
            'route' => 'admin.sensor_group_sensors.store',
            'id' => $id,
            'sensor_group_id' => $id,
        ]);

        return view('front::Sensors.create')->with($data);
    }
    
    public function store(Request $request, SensorModalHelper $sensorModalHelper, SensorGroup $sensorGroupRepo) {
        $input = $request->all();

        $sensorModalHelper->validate('create');

        $arr = $sensorModalHelper->formatInput();
        $arr['group_id'] = $input['id'];

        SensorGroupSensor::create($arr);

        $count = SensorGroupSensor::where(['group_id' => $arr['group_id']])->count();

        $sensorGroupRepo->update($arr['group_id'], [
            'count' => $count
        ]);

        return ['status' => 1];
    }

    public function edit($id, SensorModalHelper $sensorModalHelper) {
        $data = array_merge($sensorModalHelper->createData(null), [
            'route' => ['admin.sensor_group_sensors.update', $id],
            'id' => $id
        ]);

        $data['item'] = SensorGroupSensor::find($id);

        return view('front::Sensors.edit')->with($data);
    }

    public function update(Request $request, SensorModalHelper $sensorModalHelper) {
        $input = $request->all();
        $sensor = SensorGroupSensor::find($input['id']);

        $sensorModalHelper->validate('update', $sensor);

        $arr = $sensorModalHelper->formatInput();

        $sensor->update($arr);

        return ['status' => 1];
    }

    public function destroy(Request $request, SensorGroup $sensorGroupRepo) {
        $input = $request->all();
        if (!isset($input['id']) || empty($input['id']))
            return response()->json(['status' => 0]);

        $ids = $input['id'];

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $item = SensorGroupSensor::whereIn('id', $ids)->first();

        SensorGroupSensor::whereIn('id', $ids)->delete();

        $count = SensorGroupSensor::where(['group_id' => $item->group_id])->count();
        $sensorGroupRepo->update($item->group_id, [
            'count' => $count
        ]);

        return response()->json(['status' => 1, 'trigger' => 'updateSensorGroupsTable']);
    }
}
