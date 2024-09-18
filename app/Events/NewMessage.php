<?php

namespace App\Events;

use App\Events\Event;
use App\Transformers\ChatMessageTransformer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use FractalTransformer;

class NewMessage extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $message, $user;

    public function __construct($message) {
        $this->message = $message;
    }

    public function broadcastOn() {
        $channels = [];
        $channels[] = $this->message->chat->room_hash;

        foreach ($this->message->chat->participants as $participant)
        {
            if ( ! $participant->chattable)
                continue;

            if ($participant->isUser() && $participant->chattable->perm('chat', 'view')) {
                $channels[] = md5('user_'. $participant->chattable->id);
            }

            if ($participant->isDevice()) {
                $channels[] = md5('device_'. $participant->chattable->id);
            }
        }

        return $channels;
    }

    public function broadcastAs() {
        return 'message';
    }

    public function broadcastWith()
    {
        return FractalTransformer::item($this->message, ChatMessageTransformer::class)->toArray();
    }
}
