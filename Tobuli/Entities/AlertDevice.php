<?php namespace Tobuli\Entities;


class AlertDevice extends AbstractEntity {
	protected $table = 'alert_device';

    protected $fillable = array('alert_id', 'device_id', 'overspeed');

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'id', 'device_id');
    }

    public $timestamps = false;
}
