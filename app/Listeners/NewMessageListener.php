<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use App\Events\NewMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Listener;
use Illuminate\Support\Facades\Redis;
use Tobuli\Services\FcmService;

class NewMessageListener extends Listener
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
     * @param  NewMessage  $event
     * @return void
     */
    public function handle(NewMessage $event)
    {
        $fcmService = new FcmService();

        foreach ($event->message->chat->participants as $participant) {
            $fcmService->send(
                $participant->chattable,
                "New message from " . $event->message->sender_name,
                (strlen($event->message->content) > 50) ? substr($event->message->content,0,50).'...' : $event->message->content
            );
        }
    }
}
