<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeviceCameraCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $message, $camera;

    public function __construct($camera, $message) {
        $this->camera = $camera;
        $this->message = $message;
    }

    public function broadcastOn() {
        $channels = [];
        $users = $this->camera->device->users;

        foreach ($users as $user) {
            $channels[] = md5('user_'.$user->id);
        }

        return $channels;
    }

    public function broadcastAs() {
        return 'device_camera_create';
    }

    public function broadcastWith()
    {
        return $this->message;
    }
}
