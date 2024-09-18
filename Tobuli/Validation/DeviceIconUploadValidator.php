<?php namespace Tobuli\Validation;

class DeviceIconUploadValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'type' => 'required|in:icon,rotating,carros,jeepetas,motos,pasolas,fordweel,construccion,camiones,cruzroja,bicicletas,barcos,aviones,celulares,otros,personas,animales,plantaselectricas,camionetas,bus',
            'file' => 'required|image|mimes:jpeg,gif,png|max:20000|dimensions:min_width=10,min_height=10'
        ],
        'update' => [
            'type' => 'required|in:icon,rotating,carros,jeepetas,motos,pasolas,fordweel,construccion,camiones,cruzroja,bicicletas,barcos,aviones,celulares,otros,personas,animales,plantaselectricas,camionetas,bus',
            'file' => 'nullable|image|mimes:jpeg,gif,png|max:20000|dimensions:min_width=10,min_height=10'
        ]
    ];

}   //end of class


//EOF