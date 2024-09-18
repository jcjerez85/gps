<?php

namespace Tobuli\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tobuli\Entities\Event;
use Tobuli\Entities\SendQueue;

class EventWriteService
{
    /**
     * @param $events array
     */
    public function write($events)
    {
        if (empty($events))
            return;

        $this->writeEvents($events);
        $this->writeSendQueue($events);
        $this->writeEventFireAt($events);
    }

    protected function writeEvents($events)
    {
        $events = Arr::where($events, function($event, $key) {
            return !empty($event);
        });

        if (empty($events))
            return;

        $data = array_map(function(Event $event) { return $event->toArrayMassInsert();}, $events);

        Event::insert($data);
    }

    protected function writeEventFireAt($events)
    {
        $events = Arr::where($events, function($event, $key) {
            return $event->device_id && $event->alert_id;
        });

        if (empty($events))
            return;

        foreach ($events as $event) {
            DB::table('alert_device')
                ->where('alert_id',  $event->alert_id)
                ->where('device_id',  $event->device_id)
                ->update([
                    'fired_at' => $event->time
                ]);
        }
    }

    protected function writeSendQueue($events)
    {
        if (empty($events))
            return;

        SendQueue::insert(array_map(function(Event $event) {
            return (new SendQueue([
                'user_id'   => $event->user_id,
                'type'      => $event->type,
                'data'      => $event,
                'channels'  => $event->channels
            ]))->toArrayMassInsert();
            }, $events));
    }
}