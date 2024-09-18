<?php
namespace App\Transformers\Event;

use Tobuli\Entities\Event;
use Formatter;

class EventLookupTransformer extends EventTransformer {

    /**
     * @param Event $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        $event = $entity->toArray();

        $event['time'] = Formatter::time()->human($entity->time);
        $event['speed']    = Formatter::speed()->format($entity->speed);
        $event['altitude'] = Formatter::altitude()->format($entity->altitude);

        if ($device = $entity->device) {
            $event['device_name'] = $entity->device->name;
        }

        if ($geofence = $entity->geofence) {
            $event['geofence'] = [
                'id' => $geofence->id,
                'name' => $geofence->name,
            ];
        }

        unset($event['device'], $event['alert'], $event['poi']);

        return $event;
    }
}
