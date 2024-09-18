<?php namespace Tobuli\Entities;


class SmsEventQueue extends AbstractEntity {
	protected $table = 'sms_events_queue';

    protected $fillable = array('user_id', 'phone', 'message');

}
