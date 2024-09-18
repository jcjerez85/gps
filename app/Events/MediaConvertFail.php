<?php namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Tobuli\Entities\Device;
use Tobuli\Entities\File\DeviceCameraMedia;
use Tobuli\Entities\File\DeviceMedia;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\User;

class MediaConvertFail extends Event implements ShouldBroadcast {

    use SerializesModels;

    protected $actor;

    public $file;

    public $device_id;

    public $message;

    public function __construct($message, $file, $device_id, $actor) {
        $this->actor = $actor;
        $this->device_id = $device_id;
        $this->file = $file;
        $this->message = $message;
    }

    public function broadcastOn() {
        if ( ! $this->actor)
            return [];

        return [md5('user_'.$this->actor->id)];
    }

    public function broadcastAs()
    {
        return 'media_convert_fail';
    }

    public function broadcastWith()
    {
        $device = Device::find($this->device_id);

        return [
            'name' => DeviceMedia::setEntity($device)->fillAttributes($this->file)->name,
            'message' => $this->message,
            'device_id' => $this->device_id
        ];
    }

}
