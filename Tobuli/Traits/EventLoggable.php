<?php


namespace Tobuli\Traits;


use Tobuli\Entities\EventLog;

trait EventLoggable
{
    public function eventsLog()
    {
        return $this->morphMany(EventLog::class, 'object');
    }

    public function logEvent($type)
    {
        $this->eventsLog()->updateOrCreate([
            'type' => $type,
        ], [
            'time' => date('Y-m-d H:i:s'),
        ]);
    }

    public function lastLoggedEvent($type)
    {
        if ($this->eventsLog->isEmpty())
            return null;

        return $this->eventsLog->first(function ($event) use ($type) {
            return $type == $event->type;
        });
    }
}