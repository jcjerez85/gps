<?php namespace ModalHelpers;

use App\Exceptions\ResourseNotFoundException;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Repositories\DeviceSensorRepo;
use CustomFacades\Repositories\EventCustomRepo;
use CustomFacades\Validators\SensorFormValidator;
use Illuminate\Support\Str;
use Tobuli\Entities\DeviceSensor;
use Tobuli\Entities\SensorGroupSensor;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Exceptions\ValidationException;

class SensorModalHelper extends ModalHelper {

    public function paginated($device_id)
    {
        $sensors = DeviceSensorRepo::searchAndPaginate(['filter' => ['device_id' => $device_id]], 'id', 'desc', 10);

        $sensors->getCollection()->transform(function ($sensor)
        {
            $sensor->setAppends(['type_title']);
            return $sensor;
        });

        if ($this->api) {
            $sensors = $sensors->toArray();
            $sensors['url'] = route('api.get_sensors');
        }

        return $sensors;
    }

    public function createData($device_id) {
        $sensors = config('tobuli.sensors');

        ksort($sensors);

        if (!is_null($device_id)) {
            $device = DeviceRepo::find($device_id);
            $params = json_decode($device->parameters, true);
            $params = is_null($params) ? [] : $params;
            sort($params);
            $parameters = array_combine($params, $params);
        }
        else {
            $parameters = null;
        }

        return compact('sensors', 'device_id', 'parameters');
    }

    public function create() {
        $this->validate('create', null);

        $arr = $this->formatInput();

        DeviceSensorRepo::create($arr);

        return ['status' => 1];
    }

    public function editData() {
        if (array_key_exists('sensor_id', $this->data))
            $sensor_id = $this->data['sensor_id'];
        else
            $sensor_id = request()->route('sensors');
        
        $item = DeviceSensorRepo::find($sensor_id);

        $device = DeviceRepo::find($item->device_id);

        $this->checkException('devices', 'edit', $device);

        $data = $this->createData($item->device_id);

        $item->setflag = FALSE;
        if ($item->type == 'acc') {
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $item->on_value, $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $item->setflag = TRUE;
                $item->on_setflag_1 = $match['1'];
                $item->on_setflag_2 = $match['2'];
                $item->on_setflag_3 = $match['3'];
            }
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $item->off_value, $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $item->setflag = TRUE;
                $item->off_setflag_1 = $match['1'];
                $item->off_setflag_2 = $match['2'];
                $item->off_setflag_3 = $match['3'];
            }
        }

        if (in_array($item->type, ['ignition', 'door', 'engine', 'seatbelt', 'drive_business', 'drive_private', 'logical', 'route_color', 'route_color_2', 'route_color_3'])) {
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $item->on_tag_value, $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $item->setflag = TRUE;
                $item->on_tag_setflag_1 = $match['1'];
                $item->on_tag_setflag_2 = $match['2'];
                $item->on_tag_setflag_3 = $match['3'];
            }
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $item->off_tag_value, $match);
            if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
                $item->setflag = TRUE;
                $item->off_tag_setflag_1 = $match['1'];
                $item->off_tag_setflag_2 = $match['2'];
                $item->off_tag_setflag_3 = $match['3'];
            }
        }

        if (in_array($item->type, ['harsh_acceleration', 'harsh_breaking', 'harsh_turning'])) {
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\]\%/', $item->on_value, $match);
            if (isset($match['1']) && isset($match['2'])) {
                $item->setflag = TRUE;
                $item->value_setflag_1 = $match['1'];
                $item->value_setflag_2 = $match['2'];
            }
        }

        if (in_array($item->type, ['rfid', 'textual'])) {
            preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\]\%/', $item->formula, $match);
            if (isset($match['1']) && isset($match['2'])) {
                $item->setflag = TRUE;
                $item->value_setflag_1 = $match['1'];
                $item->value_setflag_2 = $match['2'];
            }
        }

        $data['item'] = $item;

        return $data;
    }

    public function edit() {
        if (array_key_exists('sensor_id', $this->data))
            $sensor_id = $this->data['sensor_id'];
        else
            $sensor_id = $this->data['id'];

        $item = DeviceSensorRepo::find($sensor_id);

        $device = DeviceRepo::find($item->device_id);

        $this->checkException('devices', 'update', $device);

        $this->validate('update', $item);

        $arr = $this->formatInput();

        if ($arr['type'] == 'odometer' && $arr['odometer_value_by'] == 'connected_odometer' && $item->value > 0)
            $arr['value_formula'] = solveEquation(['[value]' => $item->value], $arr['formula']);

        DeviceSensorRepo::update($item->id, $arr);

        return ['status' => 1];
    }

    public function destroy() {
        if (array_key_exists('sensor_id', $this->data))
            $sensor_id = $this->data['sensor_id'];
        else
            $sensor_id = request()->id;

        $item = DeviceSensorRepo::find($sensor_id);

        if (empty($item))
            throw new ResourseNotFoundException('front.sensor');

        $device = DeviceRepo::find($item->device_id);

        $this->checkException('devices', 'edit', $device);

        DeviceSensorRepo::delete($item->id);

        return ['status' => 1];
    }

    public function getProtocols() {
        if (!$this->api) {
            $devices = isset($this->data['devices']) ? $this->data['devices'] : [];
            $protocols = DeviceRepo::getProtocols($devices)->pluck('protocol', 'protocol')->all();
            $protocols = ['-' => '- '.trans('validation.attributes.protocol').' -'] + EventCustomRepo::getProtocols($this->data['type'] == '1' ? $this->user->id : NULL, $protocols)->pluck('protocol', 'protocol')->all();
        }
        else {
            $protocols = [
                [
                    'type' => 1,
                    'items' => apiArray(EventCustomRepo::getProtocols($this->user->id)->pluck('protocol', 'protocol')->all())
                ],
                [
                    'type' => 2,
                    'items' => apiArray(EventCustomRepo::getProtocols(NULL)->pluck('protocol', 'protocol')->all())
                ],
            ];
        }

        return $protocols;
    }

    public function getEvents() {
        $protocol = $this->data['protocol'];
        $where = [];
        $where['user_id'] = ($this->data['type'] == '1' ? $this->user->id : NULL);
        if (!empty($protocol) || $protocol != '-')
            $where['protocol'] = $protocol;

        $items = EventCustomRepo::getWhere($where)->pluck('message', 'id')->all();
        if ($this->api)
            $items = apiArray($items);

        return $items;
    }

    private function getParentQuery()
    {
        if (!empty($this->data['device_id'])) {
            return DeviceSensor::where('device_id', $this->data['device_id']);
        }

        if (!empty($this->data['sensor_group_id'])) {
            return SensorGroupSensor::where('group_id', $this->data['sensor_group_id']);
        }

        return null;
    }

    public function validate($type, $item = NULL) {
        if (empty($this->data['sensor_type']))
            throw new ValidationException(['sensor_type' => str_replace(':attribute', trans('validation.attributes.sensor_type'), trans('validation.required'))]);


        if (in_array($this->data['sensor_type'], ['harsh_acceleration', 'harsh_breaking', 'harsh_turning']))
            $this->data['on_value'] = $this->data['parameter_value'];

        $setflag = isset($this->data['setflag']) && $this->data['setflag'] == 1 ? TRUE : FALSE;

        if ( ! in_array($this->data['sensor_type'], ['acc', 'harsh_acceleration', 'harsh_breaking', 'harsh_turning', 'ignition', 'door', 'engine', 'logical', 'seatbelt', 'plugged']))
            $setflag = FALSE;

        SensorFormValidator::validate($this->data['sensor_type'].($setflag ? '_setflag' : ''), $this->data, null, [
            'off_setflag_1' => trans('validation.attributes.on_setflag_1'),
            'off_setflag_2' => trans('validation.attributes.on_setflag_2'),
            'off_setflag_3' => trans('validation.attributes.on_setflag_3'),
            'value_setflag_1' => trans('validation.attributes.on_setflag_1'),
            'value_setflag_2' => trans('validation.attributes.on_setflag_2'),
            'on_tag_setflag_1' => trans('validation.attributes.on_setflag_1'),
            'on_tag_setflag_2' => trans('validation.attributes.on_setflag_2'),
            'on_tag_setflag_3' => trans('validation.attributes.on_setflag_3'),
            'off_tag_setflag_1' => trans('validation.attributes.on_setflag_1'),
            'off_tag_setflag_2' => trans('validation.attributes.on_setflag_2'),
            'off_tag_setflag_3' => trans('validation.attributes.on_setflag_3'),
        ]);

        if (in_array($this->data['sensor_type'], ['acc', 'engine', 'ignition', 'engine_hours', 'rfid', 'plugged'])
            && $parentQuery = $this->getParentQuery()
        ) {
            $sensors_nr = $parentQuery->where('type', $this->data['sensor_type'])->count();

            if ($type == 'update' && $item['type'] == $this->data['sensor_type'])
                $sensors_nr--;

            if ($sensors_nr)
                throw new ValidationException(['sensor_type' => trans('front.already_has_sensor')]);
        }

        if ($this->data['sensor_type'] == 'odometer') {
            if (empty($this->data['odometer_value_by']))
                $this->data['odometer_value_by'] = 'connected_odometer';

            if ($this->data['odometer_value_by'] == 'virtual_odometer' && !array_key_exists('odometer_value', $this->data))
                throw new ValidationException(['odometer_value' => str_replace(':attribute', trans('validation.attributes.odometer_value'), trans('validation.required'))]);
            if ($this->data['odometer_value_by'] == 'connected_odometer' && empty($this->data['tag_name']))
                throw new ValidationException(['tag_name' => str_replace(':attribute', trans('validation.attributes.tag_name'), trans('validation.required'))]);
            //if ($this->data['odometer_value_by'] == 'connected_odometer' && empty($this->data['formula']))
            //    throw new ValidationException(['formula' => str_replace(':attribute', trans('validation.attributes.formula'), trans('validation.required'))]);
        }

        if (in_array($this->data['sensor_type'], ['battery', 'battery_external'])) {
            if ($this->data['shown_value_by'] == 'min_max_values') {
                if ($this->data['min_value'] == '')
                    throw new ValidationException(['min_value' => str_replace(':attribute', trans('validation.attributes.min_value'), trans('validation.required'))]);
                if ($this->data['max_value'] == '')
                    throw new ValidationException(['max_value' => str_replace(':attribute', trans('validation.attributes.max_value'), trans('validation.required'))]);
            }
            //if ($this->data['shown_value_by'] == 'formula' && empty($this->data['formula']))
            //    throw new ValidationException(['formula' => str_replace(':attribute', trans('validation.attributes.formula'), trans('validation.required'))]);
        }

        if ($this->data['sensor_type'] === 'fuel_tank_calibration'
            || $this->data['sensor_type'] === 'temperature_calibration'
            || $this->data['sensor_type'] === 'load_calibration'
        ) {
            if (count($this->data['calibrations']) < 2)
                throw new ValidationException(['calibrations' => trans('front.calibrations_min_items')]);
        }

        if (!empty($this->data['formula']) && !Str::contains($this->data['formula'], 'SETFLAG')) {
            if (is_null(solveEquation(['[value]' => 0], $this->data['formula'])))
                throw new ValidationException(['formula' => str_replace(':attribute', trans('validation.attributes.formula'), trans('validation.required'))]);
        }
    }

    public function formatInput() {
        $setflag = isset($this->data['setflag']) && $this->data['setflag'] == 1 ? TRUE : FALSE;

        if (in_array($this->data['sensor_type'], ['fuel_tank_calibration', 'temperature_calibration', 'load_calibration'])) {
            asort($this->data['calibrations']);
            foreach ($this->data['calibrations'] as $key => $value) {
                if (!is_numeric($key) || !is_numeric($value))
                    unset($this->data['calibrations'][$key]);
            }
        }

        $type = $this->data['sensor_type'];

        $arr = [
            'user_id' => $this->user->id,
            'device_id' => $this->data['device_id'],
            'name' => $this->data['sensor_name'],
            'type' => $type,
            'tag_name' => NULL,
            'add_to_history' => 0,
            'on_value' => NULL,
            'off_value' => NULL,
            'shown_value_by' => NULL,
            'fuel_tank_name' => NULL,
            'full_tank' => NULL,
            'full_tank_value' => NULL,
            'min_value' => NULL,
            'max_value' => NULL,
            'formula' => NULL,
            'odometer_value_by' => NULL,
            'odometer_value' => NULL,
            'odometer_value_unit' => 'km',
            'show_in_popup' => isset($this->data['show_in_popup']),
            'unit_of_measurement' => $this->data['unit_of_measurement'],
            'calibrations' => NULL,
            'skip_calibration' => NULL,
            'skip_empty' => NULL,
            'decbin' => NULL,
            'hexbin' => NULL,
        ];
        if (in_array($type, ['harsh_acceleration', 'harsh_breaking', 'harsh_turning'])) {
            $input_arr = [
                'tag_name' => '',
                'on_value' => '',
                'parameter_value' => ''
            ];

            if ($setflag)
                $this->data['parameter_value'] = "%SETFLAG[".$this->data['value_setflag_1'].",".$this->data['value_setflag_2']."]%";
        }
        if ($type == 'acc') {
            $input_arr = [
                'tag_name' => '',
                'on_value' => '',
                'off_value' => ''
            ];

            if ($setflag) {
                $this->data['on_value'] = "%SETFLAG[".$this->data['on_setflag_1'].",".$this->data['on_setflag_2'].",".$this->data['on_setflag_3']."]%";
                $this->data['off_value'] = "%SETFLAG[".$this->data['off_setflag_1'].",".$this->data['off_setflag_2'].",".$this->data['off_setflag_3']."]%";
            }
        }
        elseif ($type == 'battery' || $type == 'battery_external') {
            $input_arr = [
                'tag_name' => '',
                'shown_value_by' => '',
                'skip_empty' => null,
            ];
            if ($this->data['shown_value_by'] == 'min_max_values') {
                $input_arr['min_value'] = '';
                $input_arr['max_value'] = '';
            }
            elseif ($this->data['shown_value_by'] == 'formula') {
                $input_arr['formula'] = '';
            }
        }
        elseif ($type == 'fuel_tank') {
            $input_arr = [
                'tag_name' => '',
                'fuel_tank_name' => '',
                'full_tank' => '',
                'full_tank_value' => '',
                'formula' => '',
            ];
        }
        elseif ($type == 'fuel_tank_calibration') {
            $input_arr = [
                'tag_name' => '',
                'fuel_tank_name' => '',
                'calibrations' => '',
                'skip_calibration' => false,
                'formula' => '',
            ];
        }
        elseif ($type == 'fuel_consumption') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'shown_value_by' => '',
            ];
        }
        elseif ($type == 'gsm') {
            $input_arr = [
                'tag_name' => '',
                'min_value' => '',
                'max_value' => '',
                'skip_empty' => null,
            ];
        }
        elseif ($type == 'odometer') {
            $input_arr = [
                'tag_name' => '',
                'odometer_value_by' => '',
                'skip_empty' => null,
            ];
            if ($this->data['odometer_value_by'] == 'connected_odometer') {
                $input_arr['tag_name'] = '';
                $input_arr['formula'] = '';
            }
            elseif ($this->data['odometer_value_by'] == 'virtual_odometer') {
                $input_arr['odometer_value'] = '';
                $input_arr['odometer_value_unit'] = '';
                if ($this->data['odometer_value_unit'] == 'mi')
                    $this->data['odometer_value'] = milesToKilometers($this->data['odometer_value']);
            }
        }
        elseif ($type == 'satellites') {
            $input_arr = [
                'tag_name' => '',
            ];
        }
        elseif ($type == 'tachometer') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'skip_empty' => null,
            ];
        }
        elseif ($type == 'temperature') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'skip_empty' => null,
            ];
        }
        elseif ($type == 'load') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'skip_empty' => null,
            ];
        }
        elseif ($type == 'load_calibration') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'skip_empty' => null,
                'calibrations' => '',
                'skip_calibration' => false,
            ];
        }
        elseif ($type == 'temperature_calibration') {
            $input_arr = [
                'tag_name' => '',
                'calibrations' => '',
                'skip_calibration' => false,
                'formula' => '',
            ];
        }
        elseif ($type == 'engine_hours') {
            $input_arr = [
                'tag_name' => '',
                'shown_value_by' => '',
                'skip_empty' => null,
            ];
            if ($this->data['shown_value_by'] == 'logical') {
                $input_arr['on_tag_value'] = '';
                $input_arr['off_tag_value'] = '';
                $input_arr['on_type'] = '';
                $input_arr['off_type'] = '';
            }
            elseif ($this->data['shown_value_by'] == 'virtual') {
                $this->data['tag_name'] = TraccarPosition::VIRTUAL_ENGINE_HOURS_KEY;
            }
        }
        elseif ($type == 'numerical') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'skip_empty' => null,
            ];
        }
        elseif (in_array($type, ['rfid', 'textual'])) {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
            ];

            if ($setflag)
                $this->data['formula'] = "%SETFLAG[".$this->data['value_setflag_1'].",".$this->data['value_setflag_2']."]%";
        }
        elseif ($type == 'speed_ecm') {
            $input_arr = [
                'tag_name' => '',
                'formula' => '',
                'skip_empty' => null,
            ];
        }
        elseif (in_array($type, ['ignition', 'door', 'engine', 'seatbelt', 'drive_business', 'drive_private', 'logical', 'route_color', 'route_color_2', 'route_color_3', 'plugged'])) {
            $input_arr = [
                'tag_name' => '',
                'on_tag_value' => '',
                'off_tag_value' => '',
                'on_type' => '',
                'off_type' => '',
            ];

            if ($setflag) {
                $this->data['on_tag_value'] = "%SETFLAG[".$this->data['on_tag_setflag_1'].",".$this->data['on_tag_setflag_2'].",".$this->data['on_tag_setflag_3']."]%";
                $this->data['off_tag_value'] = "%SETFLAG[".$this->data['off_tag_setflag_1'].",".$this->data['off_tag_setflag_2'].",".$this->data['off_tag_setflag_3']."]%";
                $this->data['on_type'] = $this->data['on_type_setflag'];
                $this->data['off_type'] = $this->data['off_type_setflag'];
            }
        } elseif ($type == 'counter') {
            $input_arr = [
                'tag_name' => '',
                'shown_value_by' => 'logical',
                'on_tag_value' => '',
                'off_tag_value' => '',
                'on_type' => '',
                'off_type' => '',
                'value' => '',
            ];
        }

        $input_arr['add_to_history'] = '';
        $input_arr['decbin'] = '';
        $input_arr['hexbin'] = '';

        if ( ! empty($this->data['tag_name']))
            $this->data['tag_name'] = trim($this->data['tag_name']);

        return array_merge($arr, array_intersect_key($this->data, $input_arr));
    }

    public function getVirtualEngineHours($device_id)
    {
        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'show', $device);

        $engine_hours = round($device->getParameter(TraccarPosition::VIRTUAL_ENGINE_HOURS_KEY, 0) / 3600, 2);

        return [
            'device_id'    => $device_id,
            'engine_hours' => $engine_hours
        ];
    }

    public function setVirtualEngineHours($device_id)
    {
        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'edit', $device);

        $validator = \Validator::make($this->data, [
            'engine_hours' => 'required|numeric'
        ]);

        if ($validator->fails())
            throw new ValidationException(['engine_hours' => $validator->errors()->first()]);

        $engine_hours = round($this->data['engine_hours'] * 3600);

        if ($position = $device->positions()->lastest()->first()) {
            $position->setParameter(TraccarPosition::VIRTUAL_ENGINE_HOURS_KEY, $engine_hours);
            $position->save();
        }

        $device->setParameter(TraccarPosition::VIRTUAL_ENGINE_HOURS_KEY, $engine_hours);
        $device->traccar->save();

        $device->sensors()
            ->where('type', 'engine_hours')
            ->where(function($query) {
                $query->where('tag_name', TraccarPosition::VIRTUAL_ENGINE_HOURS_KEY)->orWhere('shown_value_by', 'logical');
            })
            ->update([
                'value' => $this->data['engine_hours']
            ]);

        return ['status' => 1];
    }

}