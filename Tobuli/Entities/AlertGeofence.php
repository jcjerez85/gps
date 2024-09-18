<?php namespace Tobuli\Entities;


class AlertGeofence extends AbstractEntity {
	protected $table = 'alert_geofence';

    protected $fillable = array('alert_id', 'geofence_id', 'zone', 'time_from', 'time_to');

    public $timestamps = false;

}
