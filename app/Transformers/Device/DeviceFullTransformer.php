<?php

namespace App\Transformers\Device;

use Formatter;
use Tobuli\Entities\Device;

class DeviceFullTransformer extends DeviceTransformer {

    public function transform(Device $entity)
    {
        $expirationDate = $this->canView($entity, 'expiration_date');
        $expirationDate = $expirationDate ? Formatter::time()->convert($expirationDate) : null;

        $inaccuracy = $entity->getParameter('inaccuracy');

        return [
            'id'                  => intval($entity->id),
            'active'              => (boolean)$entity->active,
            'name'                => $entity->name,
            'imei'                => $this->canView($entity, 'imei'),
            'sim_number'          => $this->canView($entity, 'sim_number'),
            'device_model'        => $entity->device_model,
            'plate_number'        => $entity->plate_number,
            'vin'                 => $entity->vin,
            'registration_number' => $entity->registration_number,
            'object_owner'        => $entity->object_owner,
            'additional_notes'    => $entity->additional_notes,

            'protocol'            => $this->canView($entity, 'protocol'),
            'expiration_date'     => $expirationDate,

            'icon_colors'         => $entity->icon_colors,
            'icon_id'             => intval($entity->icon_id),
            'timezone_id'         => is_null($entity->timezone_id) ? null : intval($entity->timezone_id),
            'fuel_measurement_id' => intval($entity->fuel_measurement_id),
            'fuel_quantity'       => floatval($entity->fuel_quantity),
            'fuel_price'          => floatval($entity->fuel_price),
            'tail_length'         => intval($entity->tail_length),
            'tail_color'          => $entity->tail_color,
            'min_moving_speed'    => intval($entity->min_moving_speed),
            'min_fuel_fillings'   => intval($entity->min_fuel_fillings),
            'min_fuel_thefts'     => intval($entity->min_fuel_thefts),
            'gprs_templates_only' => intval($entity->gprs_templates_only),

            'detect_engine'       => $entity->detect_engine,
            'engine_hours'        => $entity->engine_hours,
            'engine_status'       => $entity->getEngineStatus(),
            'stop_duration'       => $entity->stop_duration,
            'stop_duration_sec'   => $entity->getStopDuration(),
            'total_distance'      => $entity->getTotalDistance(),
            'moved_timestamp'     => $entity->moved_timestamp,
            'inaccuracy'          => is_null($inaccuracy) ? null : intval($inaccuracy),
        ];
    }
}