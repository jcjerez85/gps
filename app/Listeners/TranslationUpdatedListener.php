<?php

namespace App\Listeners;

use App\Events\TranslationUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Storage;
class TranslationUpdatedListener
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
     * @param  TranslationUpdated  $event
     * @return void
     */
    public function handle(TranslationUpdated $event)
    {
        //
    }
}
