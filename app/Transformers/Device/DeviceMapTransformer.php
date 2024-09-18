<?php

namespace App\Transformers\Device;

use Tobuli\Entities\Device;
use Formatter;

class DeviceMapTransformer extends DeviceTransformer  {

    protected $defaultIncludes = [
        'icon',
    ];

    protected static function requireLoads()
    {
        return ['icon', 'traccar', 'sensors' => function ($query) {
            $query->whereIn('type', ['acc', 'engine', 'ignition']);
        }];
    }

    public function transform(Device $entity)
    {
        $inaccuracy = $entity->getParameter('inaccuracy');

        return [
            'id'    => (int)$entity->id,
            'name'  => $entity->name,
            'tail'  => $entity->tail,
            'tail_color' => $entity->tail_color,
            'icon_color' => $entity->getStatusColor(),
            'icon_colors' => $entity->icon_colors,
            'active' => $entity->pivot ? (bool)$entity->pivot->active : null,
            'group_id' => $entity->pivot ? (int)$entity->pivot->group_id : 0,
            'online' => $entity->getStatus(),
            'lat' => $entity->lat,
            'lng' => $entity->lng,
            'speed' => $entity->speed,
            'course' => $entity->course,
            'altitude' => $entity->altitude,
            'time' => $entity->time,
            'timestamp' => (int)$entity->timestamp,
            'acktimestamp' => (int)$entity->acktimestamp,
            'engine_status' => $entity->getEngineStatus(),
            'inaccuracy' => is_null($inaccuracy) ? null : intval($inaccuracy),

            'moved_timestamp'    => (int)$entity->moved_timestamp,
            'stop_duration_sec'  => $entity->getStopDuration(),
            'total_distance'     => $entity->getTotalDistance(),
        ];
    }
}