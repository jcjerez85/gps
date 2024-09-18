<?php namespace Tobuli\Entities;

class EventLog extends AbstractEntity
{
    protected $table = 'events_log';

    protected $guarded = [];

    public $timestamps = false;

    public function object()
    {
        return $this->morphTo();
    }
}
