<?php namespace Tobuli\Entities;

use Tobuli\Traits\Searchable;

class EventCustom extends AbstractEntity
{
    use Searchable;

	protected $table = 'events_custom';

    protected $fillable = array(
        'user_id',
        'protocol',
        'conditions',
        'message',
        'always'
    );

    protected $searchable = [
        'message',
    ];

    public $timestamps = false;

    public function user() {
        return $this->belongsTo('Tobuli\Entities\User', 'user_id', 'id');
    }

    public function tags() {
        return $this->hasMany('Tobuli\Entities\EventCustomTag', 'event_custom_id', 'id');
    }

    public function port() {
        return $this->hasOne('Tobuli\Entities\TrackerPort', 'name', 'protocol');
    }

    public function setConditionsAttribute($value)
    {
        $this->attributes['conditions'] = serialize($value);
    }

    public function getConditionsAttribute($value)
    {
        return unserialize($value);
    }

    public function getMessageWithProtocolAttribute($value)
    {
        $message = $this->message;
        $user = auth()->user();

        if ($user && $user->perm('device.protocol', 'view'))
            $message .= ' (' . $this->protocol . ')';

        return $message;
    }
}
