<?php namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Device;
use Tobuli\Entities\File\DeviceCameraMedia;
use Tobuli\Entities\File\DeviceMedia;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\User;

class MediaConvertedEvent extends Event implements ShouldBroadcast {

    use SerializesModels;

    protected $actor;

    public $file;

    public $device_id;

    public function __construct($file, $device_id, $actor) {
        $this->actor = $actor;
        $this->device_id = $device_id;
        $this->file = $file;
    }

    public function broadcastOn() {
        if ( ! $this->actor)
            return [];

        return [md5('user_'.$this->actor->id)];
    }

    public function broadcastAs()
    {
        return 'media_converted';
    }

    public function broadcastWith()
    {
        $device = Device::find($this->device_id);

        return [
            'name' => DeviceMedia::setEntity($device)->fillAttributes($this->file)->name,
            'device_id' => $this->device_id
        ];
    }

}
