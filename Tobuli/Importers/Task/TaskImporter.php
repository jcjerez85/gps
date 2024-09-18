<?php

namespace Tobuli\Importers\Task;

use CustomFacades\Repositories\DeviceRepo;
use Tobuli\Entities\Device;
use Tobuli\Entities\Task;
use Tobuli\Entities\User;
use Tobuli\Importers\Importer;

class TaskImporter extends Importer
{
    protected $defaults = [
        'priority' => 2, //normal
    ];

    protected function importItem($data, $attributes = [])
    {
        $data = $this->mergeDefaults($data);
        $data = $this->parseUser($data);
        $data = $this->normalize($data);


        if (!$this->validate($data)) {
            return;
        }

        if (!$this->validateDevice($data)) {
            return;
        }

        $item = $this->getItem($data);

        if ( ! $item) {
            $this->create($data);
        }
    }

    protected function getDefaults()
    {
        return $this->defaults;
    }

    public function getValidationBaseRules(): array
    {
        return [
            'title' => 'required',
            'user_id' => 'required|exists:users,id',
            'device_id' => 'required_without:imei|exists:devices,id',
            'imei' => 'required_without:device_id|exists:devices,imei',
            'priority' => 'required|in:'.implode(',', array_keys(Task::$priorities)),
            'pickup_address' => 'required',
            'pickup_address_lat' => 'required|lat',
            'pickup_address_lng' => 'required|lng',
            'pickup_time_from' => 'required|date',
            'pickup_time_to' => 'required|date|after:pickup_time_from',
            'delivery_address' => 'required',
            'delivery_address_lat' => 'required|lat',
            'delivery_address_lng' => 'required|lng',
            'delivery_time_from' => 'required|date',
            'delivery_time_to' => 'required|date|after:delivery_time_from',
        ];
    }

    public function getFieldDescriptions(): array
    {
        return [
            'pickup_address' => 'Address is assigned to this value if specified',
            'delivery_address' => 'Address is assigned to this value if specified',
        ];
    }

    private function normalize(array &$data): array
    {
        $data['device_id'] = $this->getDeviceId($data);
        if (empty($data['device_id'])) {
            unset($data['device_id']);
        }

        $data['pickup_address'] = $this->parseAddress('pickup', $data);
        $data['delivery_address'] = $this->parseAddress('delivery', $data);

        $data['pickup_address_lat'] = $this->stripNumber($data['pickup_address_lat']);
        $data['pickup_address_lng'] = $this->stripNumber($data['pickup_address_lng']);
        $data['delivery_address_lat'] = $this->stripNumber($data['delivery_address_lat']);
        $data['delivery_address_lng'] = $this->stripNumber($data['delivery_address_lng']);

        return $data;
    }

    private function getDeviceId($data)
    {
        $deviceId = $data['device_id'] ?? null;

        if ($deviceId) {
            return $deviceId;
        }

        if (isset($data['imei'])) {
            $device = DeviceRepo::whereImei($data['imei']);
            $deviceId = $device->id ?? null;
        }

        return $deviceId;
    }

    private function parseAddress($prefix, $data)
    {
        $fieldName = $prefix.'_address';
        $fields = [
            $prefix.'_country',
            $prefix.'_city',
            $prefix.'_sector',
            $prefix.'_street',
            $prefix.'_number',
        ];

        $address = $data[$fieldName] ?? '';
        $address = trim($address);

        if ($address) {
            return $address;
        }

        foreach ($fields as $key => $field) {
            $value = $data[$field] ?? null;
            $value = trim($value);

            if (!$value) {
                continue;
            }

            $address .= $value;

            end($fields);

            if ($key !== key($fields)) {
                $address .= ', ';
            }
        }

        $address = trim($address);

        if ($address) {
            return $address;
        }

        try {
            $lat = $data[$prefix.'_address_lat'] ?? '';
            $lng = $data[$prefix.'_address_lng'] ?? '';
            $address = \CustomFacades\GeoLocation::byCoordinates($lat, $lng)->toArray();
            $address = $address['address'];
        } catch(\Exception $e) {
            $address = '';
        }

        return $address;
    }

    private function stripNumber($number)
    {
        if (preg_match('/-?\d/', $number, $matches, PREG_OFFSET_CAPTURE) !== 1) {
            return $number; //not valid number
        }

        $firstNumOccurance = $matches[0][1] ?? null;

        if (is_null($firstNumOccurance)) {
            return $number; //not valid number
        }

        $number = str_replace(',', '.', $number);
        $number = floatval(substr($number, $firstNumOccurance));

        return $number;
    }

    private function getItem($data)
    {
        return Task::where([
            'device_id' => $data['device_id'],
            'title' => $data['title'],
            'pickup_address_lat' => $data['pickup_address_lat'],
            'pickup_address_lng' => $data['pickup_address_lng'],
            'delivery_address_lat' => $data['delivery_address_lat'],
            'delivery_address_lng' => $data['delivery_address_lng'],
            'pickup_time_from' => $data['pickup_time_from'],
            'pickup_time_to' => $data['pickup_time_to'],
            'delivery_time_from' => $data['delivery_time_from'],
            'delivery_time_to' => $data['delivery_time_to'],
            'user_id' => $data['user_id'],
        ])->first();
    }

    private function create($data)
    {
        $task = new Task($data);
        $task->user_id = $data['user_id'];
        $task->save();
    }

    private function parseUser($data)
    {
        if (!isset($data['email'])) {
            return $this->setUser($data, []);
        }

        $email = $data['email'] ?? '';

        $user = User::where('email', $email)->first();

        if ($user) {
            $data['user_id'] = $user->id;

            return $data;
        }

        return $this->setUser($data, []);
    }

    protected function validateDevice($data)
    {
        $user = User::find($data['user_id']);

        if ( ! $user) {
            return false;
        }

        $device = Device::find($data['device_id']);

        return $user->can('own', $device);
    }
}
