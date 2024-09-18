<?php

namespace App\Transformers\ApiV1;

use App\Transformers\BaseTransformer;
use Formatter;
use Tobuli\Entities\Device;

class DeviceFullTransformer extends BaseTransformer {

    protected $json = false;

    protected static function requireLoads()
    {
        return ['icon', 'traccar', 'sensors', 'services', 'driver'];
    }

    public function transform(Device $entity)
    {
        $device_data = $entity->toArray();

        if (isset($device_data['users'])) {
            $filtered_users = $entity->users->filter(function ($user) {
                return $this->user->can('show', $user);
            });

            $device_data['users'] = $this->formatUserList($filtered_users);
        }

        foreach ($this->getPermissionFields() as $field) {
            if (!array_key_exists($field, $device_data))
                continue;

            if ($this->user && $this->user->can('view', $entity, $field))
                continue;

            $device_data[$field] = null;
        }

        $device_data['lastValidLatitude']  = floatval($entity->lat);
        $device_data['lastValidLongitude'] = floatval($entity->lng);
        $device_data['latest_positions']   = $entity->latest_positions;
        $device_data['icon_type'] = $entity->icon->type;


        $device_data['enable'] = intval($entity->active);
        $device_data['active'] = intval($entity->pivot->active ?? 0);
        $device_data['group_id'] = intval($entity->pivot->group_id ?? 0);

        $device_data['user_timezone_id'] = null;
        $device_data['timezone_id'] = is_null($entity->timezone_id) ? null : intval($entity->timezone_id);

        $device_data['id'] = intval($entity->id);
        $device_data['user_id'] = intval($entity->pivot->user_id  ?? 0);
        $device_data['traccar_device_id'] = intval($entity->traccar_device_id);
        $device_data['icon_id'] = intval($entity->icon_id);
        $device_data['deleted'] = intval($entity->deleted);
        $device_data['fuel_measurement_id'] = intval($entity->fuel_measurement_id);
        $device_data['tail_length'] = intval($entity->tail_length);
        $device_data['min_moving_speed'] = intval($entity->min_moving_speed);
        $device_data['min_fuel_fillings'] = intval($entity->min_fuel_fillings);
        $device_data['min_fuel_thefts'] = intval($entity->min_fuel_thefts);
        $device_data['snap_to_road'] = intval($entity->snap_to_road);
        $device_data['gprs_templates_only'] = intval($entity->gprs_templates_only);
        $device_data['group_id'] = intval($entity->pivot->group_id ?? 0);
        $device_data['current_driver_id'] = is_null($entity->current_driver_id) ? null : intval($entity->current_driver_id);
        $device_data['pivot']['user_id'] = intval($entity->pivot->user_id ?? 0);
        $device_data['pivot']['device_id'] = intval($entity->id);
        $device_data['pivot']['group_id'] = intval($entity->pivot->group_id ?? 0);
        $device_data['pivot']['current_driver_id'] = is_null($entity->current_driver_id) ? null : intval($entity->current_driver_id);
        $device_data['pivot']['timezone_id'] = null;
        $device_data['pivot']['active'] = intval($entity->pivot->active ?? 0);

        $device_data['time'] = $entity->getTime();
        $device_data['course'] = isset($entity->course) ? $entity->course : null;
        $device_data['speed'] = $entity->speed;

        $driver = $entity->driver;
        $inaccuracy = $entity->getParameter('inaccuracy');

        $sim_expiration_date = settings('plugins.additional_installation_fields.status')
            ? $this->canView($entity, 'sim_expiration_date')
            : null;


        return [
                'id'            => intval($entity->id),
                'alarm'         => is_null($this->user->alarm) ? 0 : $this->user->alarm,
                'name'          => $entity->name,
                'online'        => $entity->getStatus(),
                'time'          => $entity->time,
                'timestamp'     => $entity->timestamp,
                'acktimestamp'  => $entity->ack_timestamp,
                'lat'           => floatval($entity->lat),
                'lng'           => floatval($entity->lng),
                'course'        => (isset($entity->course) ? $entity->course : '-'),
                'speed'         => $entity->speed,
                'altitude'      => $entity->altitude,
                'icon_type'     => $entity->icon->type,
                'icon_color'    => $entity->getStatusColor(),
                'icon_colors'   => $entity->icon_colors,
                'icon'          => $entity->icon->toArray(),
                'power'         => '-',
                'address'       => '-',
                'protocol'      => $this->canView($entity, 'protocol', '-'),
                'driver'        => ($driver ? $driver->name : '-'),
                'driver_data'   => $driver ? $driver : [
                    'id' => NULL,
                    'user_id' => NULL,
                    'device_id' => NULL,
                    'name' => NULL,
                    'rfid' => NULL,
                    'phone' => NULL,
                    'email' => NULL,
                    'description' => NULL,
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ],
                'sensors'            => $this->json ? json_encode($entity->getFormatSensors()) : $entity->getFormatSensors(),
                'services'           => $this->json ? json_encode($entity->getFormatServices()) : $entity->getFormatServices(),
                'tail'               => $this->json ? json_encode($entity->tail) : $entity->tail,
                'distance_unit_hour' => $this->user->unit_of_speed,
                'unit_of_distance'   => $this->user->unit_of_distance,
                'unit_of_altitude'   => $this->user->unit_of_altitude,
                'unit_of_capacity'   => $this->user->unit_of_capacity,
                'stop_duration'      => $entity->stop_duration,
                'stop_duration_sec'  => $entity->getStopDuration() ?? 0,
                'moved_timestamp'    => $entity->moved_timestamp,
                'engine_status'      => $entity->getEngineStatus(),
                'detect_engine'      => $entity->detect_engine,
                'engine_hours'       => $entity->engine_hours,
                'total_distance'     => $entity->getTotalDistance(),
                'inaccuracy'         => is_null($inaccuracy) ? null : intval($inaccuracy),
                'sim_expiration_date'=> $sim_expiration_date ? Formatter::time()->human($sim_expiration_date) : null,
                'device_data'        => $device_data,
            ];
    }

    protected function formatUserList($users)
    {
        return $users
            ->map(function($user) {
                return [
                    'id' => intval($user['id']),
                    'email' => $user['email']
                ];
            })
            ->values()
            ->all();
    }

    protected function getPermissionFields()
    {
        static $fields = null;

        if (!is_null($fields))
            return $fields;

        $fields = [];

        $permissions = array_keys(config('tobuli.permissions'));

        foreach ($permissions as $permission) {
            try {
                list($group, $field) = explode('.', $permission);

                if ($group != 'device') {
                    continue;
                }

            } catch(\Exception $e) {
                continue;
            }

            $fields[] = $field;
        }

        return $fields;
    }
}