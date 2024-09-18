<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NoticeEvent extends Event implements ShouldBroadcast
{
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';

    use SerializesModels;

    /*
     *
     */
    protected $actor;

    public $type;
    public $message;

    public function __construct($actor, $type, $message)
    {
        $this->actor = $actor;
        $this->type = $type;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        if (!$this->actor) {
            return [];
        }

        return [md5('user_' . $this->actor->id)];
    }

    public function broadcastAs()
    {
        return 'notice';
    }
}
