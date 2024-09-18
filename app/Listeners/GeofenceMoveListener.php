<?php

namespace App\Listeners;

use App\Events\DevicePositionChanged;
use App\Events\GeofenceUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Tobuli\Entities\Geofence;
use Tobuli\Services\GeofenceService;

class GeofenceMoveListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DevicePositionChanged  $event
     * @return void
     */
    public function handle(DevicePositionChanged $event)
    {
        if (! settings('plugins.moving_geofence.status')) {
            return;
        }

        if (empty($event->device->latitude) && empty($event->device->longitude)) {
            return;
        }

        $centerCoords = [
            'lat' => $event->device->latitude,
            'lng' => $event->device->longitude,
        ];

        $service = new GeofenceService();

        Geofence::where('device_id', $event->device->id)
            ->get()
            ->each(function (Geofence $geofence, $key) use ($service, $centerCoords) {
                $this->handleGeofence($geofence, $service, $centerCoords);
            });
    }

    /**
     * Move geofence to center coords and fire GeofenceUpdatedEvent
     *
     * @param Geofence $geofence
     * @param GeofenceService $service
     * @param array $centerCoords
     * @return void
     */
    private function handleGeofence(Geofence $geofence, GeofenceService $service, $centerCoords)
    {
        $geofence = $service->moveTo($geofence, $centerCoords);
        $geofence->save();

        event(new GeofenceUpdatedEvent($geofence));
    }
}
