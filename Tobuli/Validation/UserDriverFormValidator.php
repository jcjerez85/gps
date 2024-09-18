<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Illuminate\Validation\Rule;

class UserDriverFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'name' => 'required',
            'email' => 'email',
            'devices' => 'array',
            'devices.*' => 'integer'
        ],

        'update' => [
            'name' => 'required',
            'email' => 'email',
            'devices' => 'array',
            'devices.*' => 'integer'
        ],
        'silentUpdate' => [],
    ];

    function __construct( IlluminateValidator $validator ) {
        parent::__construct( $validator );

        $user_id = auth()->user()->id ?? null;

        $this->rules['create']['rfid'] = "unique:user_drivers,rfid,NULL,id,user_id,{$user_id}";
        $this->rules['update']['rfid'] = "unique:user_drivers,rfid,%s,id,user_id,{$user_id}";
        $this->rules['silentUpdate']['rfid'] = "unique:user_drivers,rfid,%s,id,user_id,{$user_id}";
    }
}
