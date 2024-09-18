<?php namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\User;

class GeofenceUpdatedEvent extends Event implements ShouldBroadcast {

    use SerializesModels;

    /**
     * @var Geofence
     */
    public $geofence;


    public function __construct(Geofence $geofence)
    {
        $this->geofence = $geofence;
    }

    public function broadcastOn() {
        return [md5('user_'.$this->geofence->user_id)];
    }

    public function broadcastAs()
    {
        return 'geofence_updated';
    }

    public function broadcastWith()
    {
        $data = $this->geofence->toArray();
        $data['coordinates'] = json_decode($data['coordinates'], true);

        return [
            'geofence' => $data,
        ];
    }

}
