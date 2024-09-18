<?php namespace Tobuli\Entities;


class PositionGeofence extends AbstractEntity {
	protected $table = 'position_geofence';

    protected $fillable = array('position_id', 'geofence_id');

    public $timestamps = false;
}
