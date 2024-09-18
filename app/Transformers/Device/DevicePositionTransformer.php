<?php

namespace App\Transformers\Device;

use Tobuli\Entities\Device;
use Formatter;

class DevicePositionTransformer extends DeviceTransformer {

    public function transform(Device $entity)
    {
        return [
            'status'        => $entity->getStatus(),
            'lat'           => floatval($entity->lat),
            'lng'           => floatval($entity->lng),
            'speed'         => $entity->speed,
            'course'        => $entity->course,
            'altitude'      => $entity->altitude,
            'parameters'    => $entity->other,

            'time'             => Formatter::time()->human($entity->time),
            'timestamp'        => $entity->time ? strtotime($entity->time) : $entity->time,
            'ack_timestamp'    => $entity->ack_timestamp,
            'server_timestamp' => $entity->server_timestamp,
            'last_connect_timestamp' => $entity->last_connect_timestamp
        ];
    }
}