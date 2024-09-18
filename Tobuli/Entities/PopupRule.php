<?php namespace Tobuli\Entities;


class PopupRule extends AbstractEntity {
	protected $table = 'popup_rules';

    protected $fillable = ['popup_id','field_name', 'field_value', 'rule_name'];

    public $timestamps = false;


}
