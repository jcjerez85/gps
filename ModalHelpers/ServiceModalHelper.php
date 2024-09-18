<?php namespace ModalHelpers;

use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Repositories\DeviceSensorRepo;
use CustomFacades\Repositories\DeviceServiceRepo;
use CustomFacades\Validators\ServiceFormValidator;
use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Entities\Checklist;
use Auth;

class ServiceModalHelper extends ModalHelper {

    public function paginated($device_id = NULL) {
        if (is_null($device_id)) {
            $device_id = array_key_exists('device_id', $this->data) ? $this->data['device_id'] : request()->route('device_id');
        }
        $services = DeviceServiceRepo::searchAndPaginate(['filter' => ['device_id' => $device_id]], 'id', 'desc', 10);

        foreach ($services as &$service)
            $service->expires = $service->expiration();

        if ($this->api) {
            $services = $services->toArray();
            $services['url'] = route('api.get_services');
        }

        return $services;
    }

    public function createData($device_id = NULL) {
        if (is_null($device_id))
            $device_id = array_key_exists('device_id', $this->data) ? $this->data['device_id'] : request()->route('device_id');

        $device = DeviceRepo::find($device_id);

        $this->checkException('devices', 'show', $device);

        $odometerSensor = $device->getOdometerSensor();
        $odometer_value = $odometerSensor ? $odometerSensor->getValueCurrent($device->other) : '0';

        $engineHoursSensor = $device->getEngineHoursSensor();
        $engine_hours_value = $engineHoursSensor ? $engineHoursSensor->getValueCurrent($device->other) : '0';


        $expiration_by = [
            'odometer' => trans('front.odometer'),
            'engine_hours' => trans('validation.attributes.engine_hours'),
            'days' => trans('validation.attributes.days')
        ];

        return compact('device_id', 'odometer_value', 'engine_hours_value', 'expiration_by');
    }

    public function create($deviceId = null) {
        if (! is_null($deviceId)) {
            $this->data['device_id'] = $deviceId;
        }

        $this->validate('create');

        $data = $this->formatInput();

        if ( ! empty($this->data['expired'])) {
            throw new ValidationException(['id' => trans('front.service_already_expired')]);
        }

        $service = DeviceServiceRepo::create($data + ['user_id' => $this->user->id]);

        return [
            'status' => 1,
            'id' => $service->id,
        ];
    }

    public function editData($service_id = null) {
        if (is_null($service_id)) {
            $service_id = array_key_exists('service_id', $this->data) ? $this->data['service_id'] : request()->route('services');
        }

        $item = DeviceServiceRepo::find($service_id);

        $this->checkException('devices', 'show', $item->device);

        $data = $this->createData($item->device_id);

        $data['item'] = $item;
        $data['service_id'] = $service_id;

        $checklists = [];

        if (Auth::user()->can('view', new Checklist)) {
            $checklists = Checklist::with('rows')
                ->where('service_id', $service_id)
                ->orderBy('completed_at', 'asc')
                ->paginate(15);
        }

        $data['checklists'] = $checklists;

        return $data;
    }

    public function edit($service_id = null) {
        if (! is_null($service_id)) {
            $this->data['id'] = $service_id;
        }

        $item = DeviceServiceRepo::find($this->data['id']);

        $this->checkException('devices', 'show', $item->device);

        $this->validate('update');

        $input = $this->formatInput();

        if (isset($this->data['expired']) && $this->data['expired'] == 1)
            throw new ValidationException(['id' => trans('front.service_already_expired')]);

        DeviceServiceRepo::update($item->id, $input);

        return ['status' => 1];
    }

    public function destroy($service_id = null) {
        if (is_null($service_id)) {
            $service_id = array_key_exists('service_id', $this->data) ? $this->data['service_id'] : $this->data['id'];
        }

        $item = DeviceServiceRepo::find($service_id);

        if (empty($item) || !$item->device->users->contains($this->user->id))
            return ['status' => 0, 'errors' => ['id' => dontExist('front.service')]];

        DeviceServiceRepo::delete($item->id);
        return ['status' => 1];
    }

    private function validate($type) {
        ServiceFormValidator::validate($type, $this->data);

        $this->data['mobile_phone'] = isset($this->data['mobile_phone']) ? $this->data['mobile_phone'] : '';

        # Clean string, remove empty entries
        $arr = [];
        $arr['email'] = array_flip(explode(';', $this->data['email']));
        unset($arr['email']['']);
        $arr['email'] = array_flip($arr['email']);
        $arr['email'] = array_map('trim', $arr['email']);

        $arr['mobile_phone'] = array_flip(explode(';', $this->data['mobile_phone']));
        unset($arr['mobile_phone']['']);
        $arr['mobile_phone'] = array_flip($arr['mobile_phone']);

        # Regenerate string
        $this->data['email'] = implode(';', $arr['email']);
        $this->data['mobile_phone'] = implode(';', $arr['mobile_phone']);

        $validator = Validator::make($arr, [
            'email' => 'array_max:5',
            'email.*' => 'email',
        ]);

        if ($validator->fails())
            throw new ValidationException(['email' => $validator->errors()->first()]);

        $validator = Validator::make($arr, ['mobile_phone' => 'array_max:5']);
        if ($validator->fails())
            throw new ValidationException(['mobile_phone' => $validator->errors()->first()]);
    }

    private function formatInput() {
        $device = DeviceRepo::find($this->data['device_id']);

        $odometerSensor = $device->getOdometerSensor();
        $engineHoursSensor = $device->getEngineHoursSensor();

        $values = [
            'odometer' => $odometerSensor ? $odometerSensor->getValueCurrent($device->other) : 0,
            'engine_hours' => $engineHoursSensor ? $engineHoursSensor->getValueCurrent($device->other) : 0
        ];

        $this->data = prepareServiceData($this->data, $values);
        $this->data['renew_after_expiration'] = (isset($this->data['renew_after_expiration']) && $this->data['renew_after_expiration'] == 1);

        return $this->data;
    }
}