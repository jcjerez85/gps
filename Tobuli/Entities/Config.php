<?php namespace Tobuli\Entities;


class Config extends AbstractEntity {
	protected $table = 'configs';

    protected $fillable = array('title', 'value');

}
