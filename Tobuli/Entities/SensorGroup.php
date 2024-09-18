<?php namespace Tobuli\Entities;


class SensorGroup extends AbstractEntity {
	protected $table = 'sensor_groups';

    protected $fillable = array(
        'title',
        'count'
    );

    public $timestamps = false;
}
