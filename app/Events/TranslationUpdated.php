<?php
 
namespace App\Events;
 
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TranslationUpdated extends Event
{
    use SerializesModels;

    public $file;
    public $translations;

    /**
     * Create a new event instance.
     *
     * @param  String  $file
     * @param  Array  $translations
     * @return void
     */
    public function __construct($file, $translations)
    {
        $this->file = $file;
        $this->translations = $translations;
    }

    /**
    * Get the channels the event should be broadcast on.
    *
    * @return array
    */

    public function broadcastOn() {
        return [];
    }
}
