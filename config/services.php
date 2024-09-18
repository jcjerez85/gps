<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'App\User',
		'secret' => '',
	],

    'streetview' => [
        'default' => env('streetview_default', 0),
    ],

    'snaptoroad' => [
        'key' => env('snaptoroad_key', env('google_api_key', ''))
    ],

    'google_maps' => [
        'key' => env('google_api_key', '')
    ],

    'speedlimit' => [
        'key' => env('speedlimit_key', null)
    ],

    'nominatims' => [
        'http://95.216.247.190/nominatim',
        'http://95.217.44.84/nominatim',
    ]
];
