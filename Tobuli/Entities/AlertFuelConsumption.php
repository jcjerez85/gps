<?php namespace Tobuli\Entities;


class AlertFuelConsumption extends AbstractEntity {
	protected $table = 'alert_fuel_consumption';

    protected $fillable = array('alert_id', 'quantity', 'fuel_type', 'from', 'to', 'done');

    //public $timestamps = false;

    public function alert() {
        return $this->hasOne('Tobuli\Entities\Alert', 'id', 'alert_id')->with('user');
    }

}
