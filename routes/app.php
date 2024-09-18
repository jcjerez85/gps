<?php

Route::group(['middleware' => [], 'namespace' => 'Api'], function () {
    Route::get('config', function () {
        return [
            'registration' => [
                'status' => settings('main_settings.allow_users_registration') ? 1 : 0
            ],
        ];
    });

    Route::post('login', [
        'uses' => 'ApiController@login',
        'middleware' => [
            'throttle:'.config('server.api_login_throttle').',1',
        ]
    ]);

    Route::post('token', [
        'uses' => 'ApiController@login',
    ]);

    Route::group(['middleware' => ['auth.api', 'active_subscription']], function () {

        Route::get('user', [
            'uses' => ''
        ]);

        Route::get('user/config', [
            'uses' => ''
        ]);

        Route::post('fcm_token', ['uses' => 'ApiController@setFcmToken']);
    });
});


Route::get('/doc', ['as' => 'testing', 'uses' => function () {
    echo '<iframe style="position:fixed; top:0; left:0; bottom:0; right:0; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;" src="https://gpswox.api-docs.io"></iframe>';
}]);