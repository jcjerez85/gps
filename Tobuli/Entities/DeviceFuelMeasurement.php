<?php namespace Tobuli\Entities;


class DeviceFuelMeasurement extends AbstractEntity {
	protected $table = 'device_fuel_measurements';

    protected $fillable = array('title', 'fuel_title', 'distance_title', 'lang');
}
