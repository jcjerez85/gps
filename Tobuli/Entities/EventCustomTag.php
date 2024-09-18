<?php namespace Tobuli\Entities;


class EventCustomTag extends AbstractEntity {
	protected $table = 'event_custom_tags';

    protected $fillable = array(
        'event_custom_id',
        'tag',
    );

    public $timestamps = false;
}
