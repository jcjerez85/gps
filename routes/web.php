<?php

Route::group([], function () {
    Route::get('/', [
        'as' => 'home',
        'uses' => function () {
            if (Auth::check()) {
                return Redirect::route('objects.index');
            } else {
                return Redirect::route('authentication.create');
            }
        }
    ]);

    if (isPublic()) {
        Route::get('login/{hash}', ['as' => 'login', 'uses' => 'Frontend\LoginController@store']);
    } else {
        Route::get('login/{id?}', ['as' => 'login', 'uses' => 'Frontend\LoginController@create']);
    }

    Route::get('logout', ['as' => 'logout', 'uses' => 'Frontend\LoginController@destroy']);

    Route::any('authentication/store', ['as' => 'authentication.store', 'uses' => 'Frontend\LoginController@store']);
    Route::resource('authentication', 'Frontend\LoginController', ['only' => ['create', 'destroy']]);
    Route::resource('password_reminder', 'Frontend\PasswordReminderController', ['only' => ['create', 'store']]);
    Route::get('password/reset/{token}', ['uses' => 'Frontend\PasswordReminderController@reset', 'as' => 'password.reset']);
    Route::post('password/reset/{token}', ['uses' => 'Frontend\PasswordReminderController@update', 'as' => 'password.update']);

    Route::get('registration/create', ['as' => 'registration.create', 'uses' => 'Frontend\RegistrationController@create']);
    Route::post('registration/store', ['as' => 'registration.store', 'uses' => 'Frontend\RegistrationController@store'])
        ->middleware('captcha');

    Route::get('register/create', ['as' => 'register.create', 'uses' => 'Frontend\CustomRegistrationController@create']);
    Route::post('register', ['as' => 'register.store', 'uses' => 'Frontend\CustomRegistrationController@store']);
    Route::group(['middleware' => ['auth', 'active_subscription'], 'namespace' => 'Frontend'], function () {
        Route::get('register/success', ['as' => 'register.success', 'uses' => 'CustomRegistrationController@success']);
        Route::get('register/step/{step}', ['as' => 'register.step.create', 'uses' => 'CustomRegistrationController@stepCreate']);
        Route::post('register/step/{step}', ['as' => 'register.step.store', 'uses' => 'CustomRegistrationController@stepStore']);
        Route::resource('register', 'CustomRegistrationController', ['except' => ['create', 'store']]);
    });
    Route::get('verification', ['as' => 'verification', 'uses' => 'Frontend\EmailVerificationController@notice']);
    Route::get('verification/{token}', ['as' => 'verification.verify', 'uses' => 'Frontend\EmailVerificationController@verify']);

    # Exceptions to CSRF verification - \App\Http\Middleware\VerifyCsrfToken
    Route::any('payments/{gateway}/webhook', ['as' => 'payments.webhook', 'uses' => 'Frontend\PaymentsController@webhook']);
    Route::any('gpsdata_insert', ['as' => 'gpsdata_insert', 'uses' => 'Frontend\GpsDataController@insert']);
    #####

    Route::get('demo', ['as' => 'demo', 'uses' => 'Frontend\LoginController@demo']);

    Route::get('time', [
        'as' => 'time',
        'uses' => function () {
            echo date('Y-m-d H:i:s O');
        }
    ]);

    Route::any('geo_address', ['as' => 'geo_address', 'uses' => 'Frontend\AddressController@get']);
});

// Authenticated Frontend |active_subscription
Route::group(['middleware' => ['auth', 'active_subscription'], 'namespace' => 'Frontend'], function () {
    Route::delete('objects/destroy/{objects?}', ['as' => 'objects.destroy', 'uses' => 'DevicesController@destroy']);

    Route::get('objects/sidebar/groups', ['as' => 'objects.sidebar.groups', 'uses' => 'DevicesSidebarController@groups']);
    Route::get('objects/sidebar/items', ['as' => 'objects.sidebar.items', 'uses' => 'DevicesSidebarController@items']);
    Route::get('objects/sidebar', ['as' => 'objects.sidebar', 'uses' => 'DevicesSidebarController@index']);

    Route::get('objects/items', ['as' => 'objects.items', 'uses' => 'ObjectsController@items']);
    Route::get('objects/itemsSimple', ['as' => 'objects.items_simple', 'uses' => 'ObjectsController@itemsSimple']);

    Route::get('objects/items_json', ['as' => 'objects.items_json', 'uses' => 'ObjectsController@itemsJson']);
    Route::get('objects/change_group_status', ['as' => 'objects.change_group_status', 'uses' => 'ObjectsController@changeGroupStatus']);
    Route::get('objects/change_alarm_status', ['as' => 'objects.change_alarm_status', 'uses' => 'ObjectsController@changeAlarmStatus']);
    Route::get('objects/alarm_position', ['as' => 'objects.alarm_position', 'uses' => 'ObjectsController@alarmPosition']);
    Route::get('objects/stop_time/{id?}', ['as' => 'objects.stop_time', 'uses' => 'DevicesController@stopTime']);
    Route::resource('objects', 'ObjectsController', ['only' => ['index']]);



    # Lookup model
    Route::get('objects/list/settings', ['as' => 'objects.listview.edit', 'uses' => 'ObjectsListLookupController@edit']);
    Route::post('objects/list/settings', ['as' => 'objects.listview.update', 'uses' => 'ObjectsListLookupController@update']);
    Route::get('objects/list/table', ['as' => 'objects.listview.table', 'uses' => 'ObjectsListLookupController@table']);
    Route::get('objects/list/data', ['as' => 'objects.listview.data', 'uses' => 'ObjectsListLookupController@data']);
    Route::get('objects/list', ['as' => 'objects.listview', 'uses' => 'ObjectsListLookupController@index']);

    //Route::get('objects/list/items', ['as' => 'objects.listview.items', 'uses' => 'ObjectsListController@items']);

    # Lookup model
    Route::get('lookup/{lookup}/settings', ['as' => 'lookup.edit', 'uses' => 'LookupController@edit']);
    Route::post('lookup/{lookup}/settings', ['as' => 'lookup.update', 'uses' => 'LookupController@update']);
    Route::get('lookup/{lookup}/table', ['as' => 'lookup.table', 'uses' => 'LookupController@table']);
    Route::get('lookup/{lookup}/data', ['as' => 'lookup.data', 'uses' => 'LookupController@data']);
    Route::get('lookup/{lookup}/', ['as' => 'lookup.index', 'uses' => 'LookupController@index']);

    # Geofences
    Route::get('geofences/sidebar/groups', ['as' => 'geofences.sidebar.groups', 'uses' => 'GeofencesSidebarController@groups']);
    Route::get('geofences/sidebar/items', ['as' => 'geofences.sidebar.items', 'uses' => 'GeofencesSidebarController@items']);
    Route::get('geofences/sidebar', ['as' => 'geofences.sidebar', 'uses' => 'GeofencesSidebarController@index']);

    Route::get('geofences/export', ['as' => 'geofences.export', 'uses' => 'GeofencesExportController@index']);
    Route::get('geofences/export_type', ['as' => 'geofences.export_type', 'uses' => 'GeofencesExportController@getType']);
    Route::post('geofences/export_create', ['as' => 'geofences.export_create', 'uses' => 'GeofencesExportController@store']);
    Route::get('geofences/import_modal', ['as' => 'geofences.import_modal', 'uses' => 'GeofencesImportController@index']);
    Route::post('geofences/import', ['as' => 'geofences.import', 'uses' => 'GeofencesImportController@store']);
    Route::post('geofences/change_active', ['as' => 'geofences.change_active', 'uses' => 'GeofencesController@changeActive']);
    Route::put('geofences/update', ['as' => 'geofences.update', 'uses' => 'GeofencesController@update']);
    Route::any('geofences/destroy/{geofences?}', ['as' => 'geofences.destroy', 'uses' => 'GeofencesController@destroy']);
    Route::any('geofences/edit/{id?}', ['as' => 'geofences.edit', 'uses' => 'GeofencesController@edit']);
    Route::resource('geofences', 'GeofencesController', ['except' => ['update', 'destroy', 'edit']]);

    # Geofences groups
    Route::get('geofences_groups/update_select', ['as' => 'geofences_groups.update_select', 'uses' => 'GeofencesGroupsController@updateSelect']);
    Route::get('geofences_groups/change_status', ['as' => 'geofences_groups.change_status', 'uses' => 'GeofencesGroupsController@changeStatus']);
    Route::resource('geofences_groups', 'GeofencesGroupsController');

    # Geofences devices
    Route::get('geofences/devices/{id?}', ['as' => 'geofences.devices', 'uses' => 'GeofenceDevicesLookupController@index']);

    # Routes
    Route::get('routes/sidebar/groups', ['as' => 'routes.sidebar.groups', 'uses' => 'RoutesSidebarController@groups']);
    Route::get('routes/sidebar/items', ['as' => 'routes.sidebar.items', 'uses' => 'RoutesSidebarController@items']);
    Route::get('routes/sidebar', ['as' => 'routes.sidebar', 'uses' => 'RoutesSidebarController@index']);

    Route::get('route/export', ['as' => 'routes.export', 'uses' => 'RoutesExportController@index']);
    Route::post('route/export', ['as' => 'routes.export', 'uses' => 'RoutesExportController@store']);
    Route::get('routes/export_type', ['as' => 'routes.export_type', 'uses' => 'RoutesExportController@getType']);
    Route::post('route/change_active', ['as' => 'routes.change_active', 'uses' => 'RoutesController@changeActive']);
    Route::put('route/update/{id?}', ['as' => 'routes.update', 'uses' => 'RoutesController@update']);
    Route::any('route/destroy/{id?}', ['as' => 'routes.destroy', 'uses' => 'RoutesController@destroy']);
    Route::get('route/import_modal', ['as' => 'routes.import_modal', 'uses' => 'RoutesImportController@index']);
    Route::post('route/import', ['as' => 'routes.import', 'uses' => 'RoutesImportController@store']);
    Route::any('route/edit/{id?}', ['as' => 'routes.edit', 'uses' => 'RoutesController@edit']);
    Route::resource('route', 'RoutesController', ['names' => 'routes', 'except' => ['update', 'destroy', 'edit']]);

    # Route groups
    Route::resource('route_groups', 'PoisGroupsController', ['except' => ['destroy']]);

    # Widgets
    Route::get('device/widgets/location/{id?}', ['as' => 'device.widgets.location', 'uses' => 'DeviceWidgetsController@location']);
    Route::get('device/widgets/cameras/{id?}', ['as' => 'device.widgets.cameras', 'uses' => 'DeviceWidgetsController@cameras']);
    Route::get('device/widgets/image/{id?}', ['as' => 'device.widgets.image', 'uses' => 'DeviceWidgetsController@image']);
    Route::get('device/widgets/fuel_graph/{id?}', ['as' => 'device.widgets.fuel_graph', 'uses' => 'DeviceWidgetsController@fuelGraph']);
    Route::get('device/widgets/gprs_command/{id?}', ['as' => 'device.widgets.gprs_command', 'uses' => 'DeviceWidgetsController@gprsCommands']);
    Route::get('device/widgets/recent_events/{id?}', ['as' => 'device.widgets.recent_events', 'uses' => 'DeviceWidgetsController@recentEvents']);
    Route::get('device/widgets/template_webhook/{id?}', ['as' => 'device.widgets.template_webhook', 'uses' => 'DeviceWidgetsController@templateWebhook']);
    Route::post('device/widgets/template_webhook/{id?}', ['as' => 'device.widgets.template_webhook_send', 'uses' => 'DeviceWidgetsController@templateWebhookSend']);

    Route::get('devices/{device_id}/alerts', ['as' => 'device.alerts.index', 'uses' => 'DeviceAlertsController@index']);
    Route::get('devices/{device_id}/alerts/table', ['as' => 'device.alerts.table', 'uses' => 'DeviceAlertsController@table']);
    Route::get('devices/{device_id}/alerts/{alert_id}/time_period', ['as' => 'device.alerts.time_period.edit', 'uses' => 'DeviceAlertsController@editTimePeriod']);
    Route::post('devices/{device_id}/alerts/{alert_id}/time_period', ['as' => 'device.alerts.time_period.update', 'uses' => 'DeviceAlertsController@updateTimePeriod']);

    # Devices
    Route::get('devices/edit/{id}/{admin?}', ['as' => 'devices.edit', 'uses' => 'DevicesController@edit']);
    Route::post('devices/change_active', ['as' => 'devices.change_active', 'uses' => 'DevicesController@changeActive']);
    Route::get('devices/follow_map/{id?}', ['as' => 'devices.follow_map', 'uses' => 'DevicesController@followMap']);
    Route::any('devices/commands', ['as' => 'devices.commands', 'uses' => 'SendCommandController@getCommands']);
    Route::get('devices/do_destroy/{id}', ['as' => 'devices.do_destroy', 'uses' => 'DevicesController@doDestroy']);
    Route::put('devices/update', ['as' => 'devices.update', 'uses' => 'DevicesController@update']);
    Route::get('devices/do_reset_app_uuid/{id}', ['as' => 'devices.do_reset_app_uuid', 'uses' => 'DevicesController@doResetAppUuid']);
    Route::put('devices/reset_app_uuid/{id}', ['as' => 'devices.reset_app_uuid', 'uses' => 'DevicesController@resetAppUuid']);
    Route::post('devices/image/upload/{id?}', ['as' => 'device.image_upload', 'uses' => 'DevicesController@uploadImage']);
    Route::post('devices/image/delete/{id?}', ['as' => 'device.image_delete', 'uses' => 'DevicesController@deleteImage']);
    Route::get('devices/subscriptions', ['as' => 'devices.subscriptions', 'uses' => 'DeviceSubscriptionController@index']);
    Route::get('devices/subscriptions/table', ['as' => 'devices.subscriptions.table', 'uses' => 'DeviceSubscriptionController@table']);
    Route::get('devices/subscriptions/edit', ['as' => 'devices.subscriptions.edit', 'uses' => 'DeviceSubscriptionController@edit']);
    Route::get('devices/subscriptions/cancel/{id}', ['as' => 'devices.subscriptions.do_delete', 'uses' => 'DeviceSubscriptionController@doDestroy']);
    Route::delete('devices/subscriptions/cancel/{id}', ['as' => 'devices.subscriptions.delete', 'uses' => 'DeviceSubscriptionController@destroy']);
    Route::resource('devices', 'DevicesController', ['except' => ['index', 'edit', 'update']]);

    # Beacons
    Route::put('beacons/update/{id?}', ['as' => 'beacons.update', 'uses' => 'BeaconsController@update']);
    Route::resource('beacons', 'BeaconsController', ['except' => ['index', 'update', 'destroy']]);

    # Devices Groups
    Route::get('devices_groups/do_destroy/{id}', ['as' => 'devices_groups.do_destroy', 'uses' => 'DevicesGroupsController@doDestroy']);
    Route::get('devices_groups/table', ['as' => 'devices_groups.table', 'uses' => 'DevicesGroupsController@table']);
    Route::resource('devices_groups', 'DevicesGroupsController');

    # Device config
    Route::get('devices_config/index/{device_id?}', ['as' => 'device_config.index', 'uses' => 'DeviceConfigController@index']);
    Route::post('devices_config/configure', ['as' => 'device_config.configure', 'uses' => 'DeviceConfigController@configure']);
    Route::get('devices_config/getApnData/{id?}', ['as' => 'device_config.get_apn_data', 'uses' => 'DeviceConfigController@getApnData']);

    # Alerts
    Route::get('alerts/edit/{id?}', ['as' => 'alerts.edit', 'uses' => 'AlertsController@edit']);
    Route::put('alerts/update/{id?}', ['as' => 'alerts.update', 'uses' => 'AlertsController@update']);
    Route::get('alerts/do_destroy/{id?}', ['as' => 'alerts.do_destroy', 'uses' => 'AlertsController@doDestroy']);
    Route::delete('alerts/destroy/{id?}', ['as' => 'alerts.destroy', 'uses' => 'AlertsController@destroy']);
    Route::post('alerts/change_active/{active?}', ['as' => 'alerts.change_active', 'uses' => 'AlertsController@changeActive']);
    Route::any('alerts/commands', ['as' => 'alerts.commands', 'uses' => 'AlertsController@getCommands']);
    Route::any('alerts/destroy/{id?}', ['as' => 'alerts.destroy', 'uses' => 'AlertsController@destroy']);
    Route::get('alerts/summary', ['as' => 'alerts.sumary', 'uses' => 'AlertsController@summary']);
    Route::get('alerts/list', ['as' => 'alerts.index_modal', 'uses' => 'AlertsController@index_modal']);
    Route::get('alerts/list/table', ['as' => 'alerts.table', 'uses' => 'AlertsController@table']);
    Route::resource('alerts', 'AlertsController', ['except' => ['edit', 'update', 'destroy']]);

    # History
    Route::get('history', ['as' => 'history.index', 'uses' => 'HistoryController@index']);
    Route::get('history/positions', ['as' => 'history.positions', 'uses' => 'HistoryController@positionsPaginated']);
    Route::get('history/position', ['as' => 'history.position', 'uses' => 'HistoryController@getPosition']);
    Route::get('history/do_delete_positions', ['as' => 'history.do_delete_positions', 'uses' => 'HistoryController@doDeletePositions']);
    Route::any('history/delete_positions', ['as' => 'history.delete_positions', 'uses' => 'HistoryController@deletePositions']);

    Route::get('history/export', ['as' => 'history.export', 'uses' => 'HistoryExportController@generate']);
    Route::get('history/download/{file}/{name}', ['as' => 'history.download', 'uses' => 'HistoryExportController@download']);

    # Events
    Route::get('events', ['as' => 'events.index', 'uses' => 'EventsController@index']);
    Route::get('events/do_destroy', ['as' => 'events.do_destroy', 'uses' => 'EventsController@doDestroy']);
    Route::delete('events/destroy', ['as' => 'events.destroy', 'uses' => 'EventsController@destroy']);

    # Map Icons
    Route::get('pois/sidebar/groups', ['as' => 'pois.sidebar.groups', 'uses' => 'PoisSidebarController@groups']);
    Route::get('pois/sidebar/items', ['as' => 'pois.sidebar.items', 'uses' => 'PoisSidebarController@items']);
    Route::get('pois/sidebar', ['as' => 'pois.sidebar', 'uses' => 'PoisSidebarController@index']);

    Route::get('pois/export', ['as' => 'pois.export', 'uses' => 'PoisExportController@index']);
    Route::post('pois/export', ['as' => 'pois.export', 'uses' => 'PoisExportController@store']);
    Route::get('pois/export_type', ['as' => 'pois.export_type', 'uses' => 'PoisExportController@getType']);
    Route::get('pois/import', ['as' => 'pois.import', 'uses' => 'PoisImportController@index']);
    Route::post('pois/import', ['as' => 'pois.import', 'uses' => 'PoisImportController@store']);
    Route::post('pois/change_active', ['as' => 'pois.change_active', 'uses' => 'PoisController@changeActive']);
    Route::put('pois/update/{id?}', ['as' => 'pois.update', 'uses' => 'PoisController@update']);
    Route::any('pois/destroy/{id?}', ['as' => 'pois.destroy', 'uses' => 'PoisController@destroy']);
    Route::any('pois/edit/{id?}', ['as' => 'pois.edit', 'uses' => 'PoisController@edit']);
    Route::resource('pois', 'PoisController', ['except' => ['update', 'destroy', 'edit']]);

    Route::get('pois_groups/change_status', ['as' => 'pois_groups.change_status', 'uses' => 'PoisGroupsController@changeStatus']);
    Route::resource('pois_groups', 'PoisGroupsController', ['except' => ['destroy']]);

    # Report Logs
    Route::get('reports/logs', ['as' => 'reports.logs', 'uses' => 'ReportsController@logs']);
    Route::any('reports/log/download/{id}', ['as' => 'reports.log_download', 'uses' => 'ReportsController@logDownload']);
    Route::any('reports/log/destroy', ['as' => 'reports.log_destroy', 'uses' => 'ReportsController@logDestroy']);

    # Reports
    Route::any('reports/types', ['as' => 'reports.types', 'uses' => 'ReportsController@getTypes']);
    Route::any('reports/types/{type?}', ['as' => 'reports.types.show', 'uses' => 'ReportsController@getType']);
    Route::any('reports/update', ['as' => 'reports.update', 'uses' => 'ReportsController@update']);
    Route::get('reports/do_destroy/{id}', ['as' => 'reports.do_destroy', 'uses' => 'ReportsController@doDestroy']);
    Route::delete('reports/destroy', ['as' => 'reports.destroy', 'uses' => 'ReportsController@destroy']);
    Route::resource('reports', 'ReportsController', ['except' => ['edit', 'update', 'destroy']]);

    # My account
    Route::post('my_account/change_map', ['as' => 'my_account.change_map', 'uses' => 'MyAccountController@changeMap']);
    Route::get('my_account/edit', ['as' => 'my_account.edit', 'uses' => 'MyAccountController@edit']);
    Route::put('my_account/update', ['as' => 'my_account.update', 'uses' => 'MyAccountController@update']);
    Route::get('email_confirmation/resend', ['as' => 'email_confirmation.resend_code', 'uses' => 'EmailConfirmationController@resendActivationCode']);
    Route::post('email_confirmation/resend', ['as' => 'email_confirmation.resend_code_submit', 'uses' => 'EmailConfirmationController@resendActivationCodeSubmit']);
    Route::resource('email_confirmation', 'EmailConfirmationController', ['only' => ['edit', 'update']]);
    Route::get('my_account_settings/change_language/{lang}', ['as' => 'my_account_settings.change_lang', 'uses' => 'MyAccountSettingsController@changeLang']);


    # User drivers
    Route::get('user_drivers/do_destroy/{id}', ['as' => 'user_drivers.do_destroy', 'uses' => 'UserDriversController@doDestroy']);
    Route::any('user_drivers/do_update/{id}', ['as' => 'user_drivers.do_update', 'uses' => 'UserDriversController@doUpdate']);
    Route::put('user_drivers/update', ['as' => 'user_drivers.update', 'uses' => 'UserDriversController@update']);
    Route::delete('user_drivers/destroy', ['as' => 'user_drivers.destroy', 'uses' => 'UserDriversController@destroy']);
    Route::get('user_drivers/table', ['as' => 'user_drivers.table', 'uses' => 'UserDriversController@table']);
    Route::get('user_drivers/{id}/activity_log/{table?}', ['as' => 'user_drivers.activity_log', 'uses' => 'UserDriversController@activityLog']);
    Route::resource('user_drivers', 'UserDriversController', ['except' => ['update', 'destroy']]);

    # Sensors
    Route::get('sensors/do_destroy/{id}', ['as' => 'sensors.do_destroy', 'uses' => 'SensorsController@doDestroy']);
    Route::get('sensors/create/{device_id?}', ['as' => 'sensors.create', 'uses' => 'SensorsController@create']);
    Route::get('sensors/index/{device_id}', ['as' => 'sensors.index', 'uses' => 'SensorsController@index']);
    Route::get('sensors/engine_hours/{device_id?}', ['as' => 'sensors.get_engine_hours', 'uses' => 'SensorsController@getEngineHours']);
    Route::post('sensors/engine_hours/{device_id?}', ['as' => 'sensors.set_engine_hours', 'uses' => 'SensorsController@setEngineHours']);
    Route::delete('sensors/destroy', ['as' => 'sensors.destroy', 'uses' => 'SensorsController@destroy']);
    Route::resource('sensors', 'SensorsController', ['only' => ['store', 'edit', 'update']]);
    Route::get('sensors/param/{param}/{device_id}', ['as' => 'sensors.param', 'uses' => 'SensorsController@parameterSuggestion']);

    # Services
    Route::get('services/do_destroy/{id}', ['as' => 'services.do_destroy', 'uses' => 'ServicesController@doDestroy']);
    Route::get('services/create/{device_id?}', ['as' => 'services.create', 'uses' => 'ServicesController@create']);
    Route::get('services/index/{device_id?}', ['as' => 'services.index', 'uses' => 'ServicesController@index']);
    Route::get('services/table/{device_id?}', ['as' => 'services.table', 'uses' => 'ServicesController@table']);
    Route::put('services/update/{id?}', ['as' => 'services.update', 'uses' => 'ServicesController@update']);
    Route::delete('services/destroy', ['as' => 'services.destroy', 'uses' => 'ServicesController@destroy']);
    Route::resource('services', 'ServicesController', ['only' => ['store', 'edit']]);

    # Custom events
    Route::get('custom_events/do_destroy/{id}', ['as' => 'custom_events.do_destroy', 'uses' => 'CustomEventsController@doDestroy']);
    Route::post('custom_events/get_events', ['as' => 'custom_events.get_events', 'uses' => 'CustomEventsController@getEvents']);
    Route::post('custom_events/get_protocols', ['as' => 'custom_events.get_protocols', 'uses' => 'CustomEventsController@getProtocols']);
    Route::any('custom_events/get_events_by_device', ['as' => 'custom_events.get_events_by_device', 'uses' => 'CustomEventsController@getEventsByDevices']);
    Route::put('custom_events/update', ['as' => 'custom_events.update', 'uses' => 'CustomEventsController@update']);
    Route::delete('custom_events/destroy', ['as' => 'custom_events.destroy', 'uses' => 'CustomEventsController@destroy']);
    Route::get('custom_events/table', ['as' => 'custom_events.table', 'uses' => 'CustomEventsController@table']);
    Route::resource('custom_events', 'CustomEventsController', ['except' => ['update', 'destroy']]);

    # User sms templates
    Route::get('user_sms_templates/do_destroy/{id}', ['as' => 'user_sms_templates.do_destroy', 'uses' => 'UserSmsTemplatesController@doDestroy']);
    Route::post('user_sms_templates/get_message', ['as' => 'user_sms_templates.get_message', 'uses' => 'UserSmsTemplatesController@getMessage']);
    Route::put('user_sms_templates/update', ['as' => 'user_sms_templates.update', 'uses' => 'UserSmsTemplatesController@update']);
    Route::delete('user_sms_templates/destroy', ['as' => 'user_sms_templates.destroy', 'uses' => 'UserSmsTemplatesController@destroy']);
    Route::get('user_sms_templates/table', ['as' => 'user_sms_templates.table', 'uses' => 'UserSmsTemplatesController@table']);
    Route::resource('user_sms_templates', 'UserSmsTemplatesController', ['except' => ['update', 'destroy']]);

    # User gprs templates
    Route::get('user_gprs_templates/do_destroy/{id}', ['as' => 'user_gprs_templates.do_destroy', 'uses' => 'UserGprsTemplatesController@doDestroy']);
    Route::post('user_gprs_templates/get_message', ['as' => 'user_gprs_templates.get_message', 'uses' => 'UserGprsTemplatesController@getMessage']);
    Route::put('user_gprs_templates/update', ['as' => 'user_gprs_templates.update', 'uses' => 'UserGprsTemplatesController@update']);
    Route::delete('user_gprs_templates/destroy', ['as' => 'user_gprs_templates.destroy', 'uses' => 'UserGprsTemplatesController@destroy']);
    Route::get('user_gprs_templates/table', ['as' => 'user_gprs_templates.table', 'uses' => 'UserGprsTemplatesController@table']);
    Route::resource('user_gprs_templates', 'UserGprsTemplatesController', ['except' => ['update', 'destroy']]);

    Route::get('language', ['as' => 'languages.index', 'uses' => 'LanguageController@index']);

    #My account settings
    Route::get('my_account_settings/change_top_toolbar', ['as' => 'my_account_settings.change_top_toolbar', 'uses' => 'MyAccountSettingsController@changeTopToolbar']);
    Route::get('my_account_settings/change_map_settings', ['as' => 'my_account_settings.change_map_settings', 'uses' => 'MyAccountSettingsController@changeMapSettings']);
    Route::get('my_account_settings/edit', ['as' => 'my_account_settings.edit', 'uses' => 'MyAccountSettingsController@edit']);
    Route::get('tools/menu', ['as' => 'tools.menu', 'uses' => 'MyAccountSettingsController@menu']);
    Route::put('my_account_settings/update', ['as' => 'my_account_settings.update', 'uses' => 'MyAccountSettingsController@update']);


    # Send command
    Route::post('send_command/gprs', ['as' => 'send_command.gprs', 'uses' => 'SendCommandController@gprsStore']);
    Route::get('send_command/get_device_sim_number', ['as' => 'send_command.get_device_sim_number', 'uses' => 'SendCommandController@getDeviceSimNumber']);
    Route::resource('send_command', 'SendCommandController', ['only' => ['create', 'store']]);

    #Camera
    Route::get('device_media/create', ['as' => 'device_media.create', 'uses' => 'DeviceMediaController@create']);
    Route::get('device_media/images/{device_id?}', ['as' => 'device_media.get_images', 'uses' => 'DeviceMediaController@getImages']);
    Route::get('device_media/images_table/{device_id?}', ['as' => 'device_media.get_images_table', 'uses' => 'DeviceMediaController@getImagesTable']);
    Route::get('device_media/image/{device_id?}/{file_name?}', ['as' => 'device_media.get_image', 'uses' => 'DeviceMediaController@getImage']);
    Route::get('device_media/download/{device_id?}/{file_name?}', ['as' => 'device_media.download_file', 'uses' => 'DeviceMediaController@download']);
    Route::get('device_media/delete/{device_id?}/{file_name?}', ['as' => 'device_media.delete_image', 'uses' => 'DeviceMediaController@remove']);
    Route::delete('device_media/delete/{device_id?}', ['as' => 'device_media.delete_images', 'uses' => 'DeviceMediaController@removeMulti']);
    Route::post('device_media/download/{device_id?}', ['as' => 'device_media.download_images', 'uses' => 'DeviceMediaController@downloadMulti']);
    Route::get('device_media/file/{device_id?}/{file_name?}', ['as' => 'device_media.display_image', 'uses' => 'DeviceMediaController@getFile']);
    Route::get('device_media/camera/file/{camera_id?}/{file_name?}', ['as' => 'device_media.display_camera_image', 'uses' => 'DeviceMediaController@getCameraFile']);

    # Secondary credentials
    Route::get('secondary_credentials', ['as' => 'secondary_credentials.index', 'uses' => 'SecondaryCredentialsController@index']);
    Route::get('secondary_credentials/table', ['as' => 'secondary_credentials.table', 'uses' => 'SecondaryCredentialsController@table']);
    Route::get('secondary_credentials/create', ['as' => 'secondary_credentials.create', 'uses' => 'SecondaryCredentialsController@create']);
    Route::post('secondary_credentials', ['as' => 'secondary_credentials.store', 'uses' => 'SecondaryCredentialsController@store']);
    Route::put('secondary_credentials/{id}', ['as' => 'secondary_credentials.update', 'uses' => 'SecondaryCredentialsController@update']);
    Route::get('secondary_credentials/edit/{id}', ['as' => 'secondary_credentials.edit', 'uses' => 'SecondaryCredentialsController@edit']);
    Route::any('secondary_credentials/destroy', ['as' => 'secondary_credentials.destroy', 'middleware' => ['confirmed_action'], 'uses' => 'SecondaryCredentialsController@destroy']);

    # Media categories
    Route::get('media_categories', ['as' => 'media_categories.index', 'uses' => 'MediaCategoriesController@index']);
    Route::get('media_categories/table', ['as' => 'media_categories.table', 'uses' => 'MediaCategoriesController@table']);
    Route::get('media_categories/create', ['as' => 'media_categories.create', 'uses' => 'MediaCategoriesController@create']);
    Route::get('media_categories/edit/{id}', ['as' => 'media_categories.edit', 'uses' => 'MediaCategoriesController@edit']);
    Route::get('media_categories/do_destroy/{id}', ['as' => 'media_categories.do_destroy', 'uses' => 'MediaCategoriesController@doDestroy']);
    Route::delete('media_categories/{id}', ['as' => 'media_categories.destroy', 'uses' => 'MediaCategoriesController@destroy']);
    Route::put('media_categories/{id}', ['as' => 'media_categories.update', 'uses' => 'MediaCategoriesController@update']);
    Route::post('media_categories', ['as' => 'media_categories.store', 'uses' => 'MediaCategoriesController@store']);

    #Device cameras
    Route::get('device_camera/index/{device_id}', ['as' => 'device_camera.index', 'uses' => 'DeviceCamerasController@index']);
    Route::get('device_camera/create/{device_id}', ['as' => 'device_camera.create', 'uses' => 'DeviceCamerasController@create']);
    Route::get('device_camera/do_destroy/{id}', ['as' => 'device_camera.do_destroy', 'uses' => 'DeviceCamerasController@doDestroy']);
    Route::put('device_camera/update', ['as' => 'device_camera.update', 'uses' => 'DeviceCamerasController@update']);
    Route::resource('device_camera', 'DeviceCamerasController', ['only' => ['store', 'edit', 'destroy']]);

    # SMS gateway
    Route::get('sms_gateway/test_sms', ['as' => 'sms_gateway.test_sms', 'uses' => 'SmsGatewayController@testSms']);
    Route::post('sms_gateway/send_test_sms', ['as' => 'sms_gateway.send_test_sms', 'uses' => 'SmsGatewayController@sendTestSms']);
    Route::get('sms_gateway/clear_queue', ['as' => 'sms_gateway.clear_queue', 'uses' => 'SmsGatewayController@clearQueue']);

    Route::get('maintenance/list', ['as' => 'maintenance.table', 'uses' => 'MaintenanceController@table']);
    Route::get('maintenance/{imei?}', ['as' => 'maintenance.index', 'uses' => 'MaintenanceController@index']);

    # Tasks
    Route::get('tasks/list', ['as'=> 'tasks.list', 'uses' => 'TasksController@search']);
    Route::get('tasks/do_destroy/{id?}', ['as' => 'tasks.do_destroy', 'uses' => 'TasksController@doDestroy']);
    Route::get('tasks/signature/{taskStatusId}', ['as' => 'tasks.signature', 'uses' => 'TasksController@getSignature']);
    Route::get('tasks/import', ['as' => 'tasks.import', 'uses' => 'TasksController@import']);
    Route::post('tasks/import', ['as' => 'tasks.import_set', 'uses' => 'TasksController@importSet']);
    Route::get('tasks/assign', ['as' => 'tasks.assign_form', 'uses' => 'TasksController@assignForm']);
    Route::post('tasks/assign', ['as' => 'tasks.assign', 'uses' => 'TasksController@assign']);
    Route::put('tasks/update', ['as' => 'tasks.update', 'uses' => 'TasksController@update']);
    Route::delete('tasks/destroy', ['as' => 'tasks.destroy', 'uses' => 'TasksController@destroy']);
    Route::resource('tasks', 'TasksController', ['except' => ['update', 'destroy']]);

    # Importer
    Route::post('import/get_fields', ['as' => 'import.get_fields', 'uses' => 'ImportController@getFields']);

    Route::any('address/autocomplete', ['as' => 'address.autocomplete', 'uses' => 'AddressController@autocomplete']);
    Route::any('address/reverse', ['as' => 'address.reverse', 'uses' => 'AddressController@reverse']);
    Route::any('address/search', ['as' => 'address.search', 'uses' => 'AddressController@search']);
    Route::get('address/map', ['as' => 'address.map', 'uses' => 'AddressController@map']);
    Route::any('address', ['as' => 'address.get', 'uses' => 'AddressController@get']);

    # Chats
    Route::get('chat/index',['as' => 'chat.index', 'uses' =>  'ChatController@index']);
    Route::get('chat/unread/count', ['as' => 'chat.unread_msg_count', 'uses' =>  'ChatController@getUnreadMessagesCount']);
    Route::get('chat/init/{chatableId}/{type?}',['as' => 'chat.init', 'uses' =>  'ChatController@initChat']);
    Route::get('chat/participants', ['as' => 'chat.searchParticipant', 'uses' =>  'ChatController@searchParticipant']);
    Route::get('chat/{chatId}/messages', ['as' => 'chat.messages', 'uses' => 'ChatController@getMessages']);
    Route::get('chat/{chatId}',['as' => 'chat.get', 'uses' =>  'ChatController@getChat']);
    Route::post('chat/{chatId}', ['as' => 'chat.message', 'uses' => 'ChatController@createMessage']);

    # Dashboard
    Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);
    Route::get('dashboard/block_content', ['as' => 'dashboard.block_content', 'uses' => 'DashboardController@blockContent']);
    Route::post('dashboard/config_update', ['as' => 'dashboard.config_update', 'uses' => 'DashboardController@updateConfig']);

    # Command Schedules
    Route::resource('command_schedules', 'CommandSchedulesController', ['except' => 'show']);
    Route::get('command_schedules/logs/{id}', ['as' => 'command_schedules.logs', 'uses' =>  'CommandSchedulesController@logs']);

    # Device expenses
    Route::get('device_expenses/index/{device_id?}', ['as' => 'device_expenses.index', 'uses' => 'DeviceExpensesController@index']);
    Route::get('device_expenses/table/{device_id?}', ['as' => 'device_expenses.table', 'uses' => 'DeviceExpensesController@table']);
    Route::get('device_expenses/modal/{device_id?}', ['as' => 'device_expenses.modal', 'uses' => 'DeviceExpensesController@modal']);
    Route::get('device_expenses/suppliers', ['as' => 'device_expenses.suppliers', 'uses' => 'DeviceExpensesController@suppliers']);
    Route::resource('device_expenses', 'DeviceExpensesController', ['except' => ['index']]);

    #Sharing
    Route::get('sharing/index', ['as' => 'sharing.index', 'uses' => 'SharingController@index']);
    Route::get('sharing/table', ['as' => 'sharing.table', 'uses' => 'SharingController@table']);
    Route::get('sharing/edit/{sharing_id}', ['as' => 'sharing.edit', 'uses' => 'SharingController@edit']);
    Route::put('sharing/update/{sharing_id}', ['as' => 'sharing.update', 'uses' => 'SharingController@update']);
    Route::get('sharing/create', ['as' => 'sharing.create', 'uses' => 'SharingController@create']);
    Route::post('sharing/store', ['as' => 'sharing.store', 'uses' => 'SharingController@store']);
    Route::get('sharing/do_destroy/{sharing_id}', ['as' => 'sharing.do_destroy', 'uses' => 'SharingController@doDestroy']);
    Route::delete('sharing/destory', ['as' => 'sharing.destroy', 'uses' => 'SharingController@destroy']);
    Route::post('sharing/share', ['as' => 'sharing.share', 'uses' => 'SharingController@createInstant']);
    Route::get('sharing/send', ['as' => 'sharing.send_form', 'uses' => 'SharingController@sendForm']);
    Route::post('sharing/send', ['as' => 'sharing.send', 'uses' => 'SharingController@send']);

    Route::get('sharing/device/{device_id}', ['as' => 'sharing.device_sharing', 'uses' => 'SharingDeviceController@index']); //@TODO: not used
    Route::get('sharing/device/{device_id}/table', ['as' => 'sharing.device_table', 'uses' => 'SharingDeviceController@table']); //@TODO: not used
    Route::get('sharing/device/{device_id}/add_to_sharing', ['as' => 'sharing_device.add_to_sharing', 'uses' => 'SharingDeviceController@addToSharing']);
    Route::post('sharing/device/{device_id}/save_to_sharing', ['as' => 'sharing_device.save_to_sharing', 'uses' => 'SharingDeviceController@saveToSharing']);
    Route::get('sharing/device/{device_id}/do_destroy/{sharing_id}', ['as' => 'sharing_device.do_destroy', 'uses' => 'SharingDeviceController@doDestroy']);
    Route::delete('sharing/device/{device_id}/destory/{sharing_id}/', ['as' => 'sharing_device.destroy', 'uses' => 'SharingDeviceController@destroy']);
    /*
         #Sharing device
         Route::get('sharing_device/{sharing_id}/create', ['as' => 'sharing_device.create', 'uses' => 'SharingDeviceController@create']);
         Route::post('sharing_device/{sharing_id}/store', ['as' => 'sharing_device.store', 'uses' => 'SharingDeviceController@store']);
         Route::get('sharing_device/{sharing_id}/edit/{device_id}', ['as' => 'sharing_device.edit', 'uses' => 'SharingDeviceController@edit']);
         Route::post('sharing_device/{sharing_id}/update/{device_id}', ['as' => 'sharing_device.update', 'uses' => 'SharingDeviceController@update']);
         Route::get('sharing_device/{sharing_id}/table', ['as' => 'sharing_device.table', 'uses' => 'SharingDeviceController@table']);
         Route::get('sharing_device/do_destroy/{sharing_id}/{device_id}', ['as' => 'sharing_device.do_destroy', 'uses' => 'SharingDeviceController@doDestroy']);
         Route::delete('sharing_device/destory/{id}/{device_id}', ['as' => 'sharing_device.destroy', 'uses' => 'SharingDeviceController@destroy']);
    */

    #Lock status
    Route::get('lock_status/history/{deviceId?}', ['as' => 'lock_status.history', 'uses' => 'LockStatusController@history']);
    Route::get('lock_status/table/{deviceId?}', ['as' => 'lock_status.table', 'uses' => 'LockStatusController@table']);
    Route::get('lock_status/status/{deviceId?}', ['as' => 'lock_status.status', 'uses' => 'LockStatusController@lockStatus']);
    Route::get('lock_status/unlock/{deviceId?}', ['as' => 'lock_status.unlock', 'uses' => 'LockStatusController@unlock']);
    Route::post('lock_status/do_unlock/', ['as' => 'lock_status.do_unlock', 'uses' => 'LockStatusController@doUnlock']);

    # Checklists
    Route::get('checklists/index/{service_id}', ['as' => 'checklists.index', 'uses' => 'ChecklistsController@index']);
    Route::get('checklists/table/{service_id?}', ['as' => 'checklists.table', 'uses' => 'ChecklistsController@table']);
    Route::put('checklists/update/{checklist_id}', ['as' => 'checklists.update', 'uses' => 'ChecklistsController@update']);
    Route::get('checklists/create/{service_id?}', ['as' => 'checklists.create', 'uses' => 'ChecklistsController@create']);
    Route::post('checklists/store/{service_id}', ['as' => 'checklists.store', 'uses' => 'ChecklistsController@store']);
    Route::get('checklists/do_destroy/{checklist_id}', ['as' => 'checklists.do_destroy', 'uses' => 'ChecklistsController@doDestroy']);
    Route::delete('checklists/destory', ['as' => 'checklists.destroy', 'uses' => 'ChecklistsController@destroy']);
    Route::post('checklists/upload_file/{row_id?}', ['as' => 'checklists.upload_file', 'uses' => 'ChecklistsController@upload']);
    Route::post('checklists/update_row_status/{row_id?}', ['as' => 'checklists.update_row_status', 'uses' => 'ChecklistsController@updateRowStatus']);
    Route::post('checklists/update_row_outcome/{row_id?}', ['as' => 'checklists.update_row_outcome', 'uses' => 'ChecklistsController@updateRowOutcome']);
    Route::post('checklists/sign_checklist/{checklist_id?}', ['as' => 'checklists.sign_checklist', 'uses' => 'ChecklistsController@sign']);
    Route::post('checklists/delete_image/{image_id}', ['as' => 'checklists.delete_image', 'uses' => 'ChecklistsController@deleteImage']);
    Route::get('checklists/get_checklists/{service_id}', ['as' => 'checklists.get_checklists', 'uses' => 'ChecklistsController@getChecklists']);
    Route::get('checklists/get_row/{row_id?}', ['as' => 'checklists.get_row', 'uses' => 'ChecklistsController@getRow']);
    Route::get('checklists/preview/{checklist_id}', ['as' => 'checklists.preview', 'uses' => 'ChecklistsController@preview']);
    Route::get('checklists/edit/{checklist_id}', ['as' => 'checklists.edit', 'uses' => 'ChecklistsController@edit']);
    Route::get('checklists/qr_code/preview/{device_id}', ['as' => 'checklist.qr_code_preview', 'uses' => 'ChecklistsController@qrCode']);
    Route::get('checklists/qr_code/image/{device_id}', ['as' => 'checklist.qr_code_image', 'uses' => 'ChecklistsController@qrCodeImage']);
    Route::get('checklists/qr_code/download/{device_id}', ['as' => 'checklist.qr_code_download', 'uses' => 'ChecklistsController@downloadQrCode']);

    # Checklist templates
    Route::get('checklist_template/index', ['as' => 'checklist_template.index', 'uses' => 'ChecklistTemplateController@index']);
    Route::get('checklist_template/table', ['as' => 'checklist_template.table', 'uses' => 'ChecklistTemplateController@table']);
    Route::get('checklist_template/edit/{template_id}', ['as' => 'checklist_template.edit', 'uses' => 'ChecklistTemplateController@edit']);
    Route::put('checklist_template/update/{template_id}', ['as' => 'checklist_template.update', 'uses' => 'ChecklistTemplateController@update']);
    Route::get('checklist_template/create', ['as' => 'checklist_template.create', 'uses' => 'ChecklistTemplateController@create']);
    Route::post('checklist_template/store', ['as' => 'checklist_template.store', 'uses' => 'ChecklistTemplateController@store']);
    Route::get('checklist_template/do_destroy/{template_id}', ['as' => 'checklist_template.do_destroy', 'uses' => 'ChecklistTemplateController@doDestroy']);
    Route::delete('checklist_template/destory', ['as' => 'checklist_template.destroy', 'uses' => 'ChecklistTemplateController@destroy']);

    # Call actions
    Route::get('call_actions/index', ['as' => 'call_actions.index', 'uses' => 'CallActionsController@index']);
    Route::get('call_actions/table', ['as' => 'call_actions.table', 'uses' => 'CallActionsController@table']);
    Route::get('call_actions/create/{device_id}', ['as' => 'call_actions.create', 'uses' => 'CallActionsController@create']);
    Route::get('call_actions/create_by_event/{event_id}', ['as' => 'call_actions.create_by_event', 'uses' => 'CallActionsController@createByEvent']);
    Route::post('call_actions/store', ['as' => 'call_actions.store', 'uses' => 'CallActionsController@store']);
    Route::get('call_actions/edit/{id}', ['as' => 'call_actions.edit', 'uses' => 'CallActionsController@edit']);
    Route::put('call_actions/update/{id}', ['as' => 'call_actions.update', 'uses' => 'CallActionsController@update']);
    Route::delete('call_actions/destory/{id}', ['as' => 'call_actions.destroy', 'uses' => 'CallActionsController@destroy']);

    #Device plans
    Route::get('device_plans/{device_id?}', ['as' => 'device_plans.index', 'uses' => 'DevicePlansController@index']);
    Route::get('device_plans/plans/{device_id?}', ['as' => 'device_plan.plans', 'uses' => 'DevicePlansController@plans']);

    #Device routes type
    Route::get('device/route_type/{id}', ['as' => 'device_route_type.edit', 'uses' => 'DeviceRoutesTypeController@edit']);
    Route::post('device/route_type/{id}', ['as' => 'device_route_type.update', 'uses' => 'DeviceRoutesTypeController@update']);
    Route::delete('device/route_type/{id}', ['as' => 'device_route_type.destroy', 'uses' => 'DeviceRoutesTypeController@destroy']);
    Route::get('device/{device_id}/route_type', ['as' => 'device_route_type.show', 'uses' => 'DeviceRoutesTypeController@show']);
    Route::get('device/{device_id}/route_type/table', ['as' => 'device_route_type.table', 'uses' => 'DeviceRoutesTypeController@table']);
    Route::get('device/{device_id}/route_type/create', ['as' => 'device_route_type.create', 'uses' => 'DeviceRoutesTypeController@create']);
    Route::post('device/{device_id}/route_type', ['as' => 'device_route_type.store', 'uses' => 'DeviceRoutesTypeController@store']);

    Route::get('forwards/table', ['as' => 'forwards.table', 'uses' => 'ForwardsController@table']);
    Route::any('forwards/destroy', ['as' => 'forwards.destroy', 'middleware' => ['confirmed_action'], 'uses' => 'ForwardsController@destroy']);
    Route::resource('forwards', 'ForwardsController', ['except' => ['destroy']]);
});

// Authenticated Admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'auth.manager', 'active_subscription'], 'namespace' => 'Admin'], function () {
    Route::get('/', ['as' => 'admin', 'uses' => function () {
        if (auth()->user()->can('view', new \Tobuli\Entities\User()))
            return Redirect::route('admin.clients.index');

        return Redirect::route('admin.objects.index');
    }]);

    Route::group(['as' => 'admin.'], function() {
        # Clients
        Route::get('users/clients/import_geofences', ['as' => 'clients.import_geofences', 'uses' => 'ClientsController@importGeofences']);
        Route::post('users/clients/import_geofences', ['as' => 'clients.import_geofences_set', 'uses' => 'ClientsController@importGeofencesSet']);
        Route::get('users/clients/import_poi', ['as' => 'clients.import_poi', 'uses' => 'ClientsController@importPoi']);
        Route::post('users/clients/import_poi', ['as' => 'clients.import_poi_set', 'uses' => 'ClientsController@importPoiSet']);
        Route::get('users/clients/import_routes', ['as' => 'clients.import_routes', 'uses' => 'ClientsController@importRoutes']);
        Route::post('users/clients/import_routes', ['as' => 'clients.import_routes_set', 'uses' => 'ClientsController@importRoutesSet']);
        Route::any('users/clients', ['as' => 'clients.index', 'uses' => 'ClientsController@index']);
        Route::any('users/clients/get_devices/{id}', ['as' => 'clients.get_devices', 'uses' => 'ClientsController@getDevices']);
        Route::any('users/clients/get_permissions_table', ['as' => 'clients.get_permissions_table', 'uses' => 'ClientsController@getPermissionsTable']);
        Route::put('users/clients/update', ['as' => 'clients.update', 'uses' => 'ClientsController@update']);
        Route::get('users/clients/do_destroy', ['as' => 'clients.do_destroy', 'uses' => 'ClientsController@doDestroy']);
        Route::any('users/clients/destroy/{id?}', ['as' => 'clients.destroy', 'uses' => 'ClientsController@destroy']);
        Route::post('users/clients/active/{active}', ['as' => 'clients.set_active', 'uses' => 'ClientsController@setActiveMulti']);
        Route::post('users/clients/{id}/login_token', ['as' => 'clients.set_login_token', 'uses' => 'ClientsController@setLoginToken']);
        Route::delete('users/clients/{id}/login_token', ['as' => 'clients.unset_login_token', 'uses' => 'ClientsController@unsetLoginToken']);
        Route::get('users/clients/{id?}/login_periods', ['as' => 'clients.login_periods', 'uses' => 'ClientLoginPeriodsController@get']);
        Route::get('users/clients/{id}/forwards', ['as' => 'clients.forwards', 'uses' => 'ClientForwardsController@get']);
        Route::resource('clients', 'ClientsController', ['except' => ['index', 'destroy', 'update']]);
        Route::get('client/devices', ['as' => 'client.devices.index', 'uses' => 'ClientDevicesController@index']);
        Route::get('client/{user_id}/devices', ['as' => 'client.devices.get', 'uses' => 'ClientDevicesController@get']);

        # Login as
        Route::get('login_as/{id}', ['as' => 'clients.login_as', 'uses' => 'ClientsController@loginAs']);
        Route::get('login_as_agree/{id}', ['as' => 'clients.login_as_agree', 'uses' => 'ClientsController@loginAsAgree']);

        # Objects
        Route::get('objects/assign', ['as' => 'objects.assignForm', 'uses' => 'ObjectsUsersController@assignForm']);
        Route::post('objects/assign', ['as' => 'objects.assign', 'uses' => 'ObjectsUsersController@assign']);
        Route::any('users/objects', ['as' => 'objects.index', 'uses' => 'ObjectsController@index']);
        Route::get('objects/import', ['as' => 'objects.import', 'uses' => 'ObjectsController@import']);
        Route::post('objects/export', ['as' => 'objects.export', 'uses' => 'ObjectsController@export']);
        Route::get('objects/export', ['as' => 'objects.export_modal', 'uses' => 'ObjectsController@exportModal']);
        Route::post('objects/bulk_delete', ['as' => 'objects.bulk_delete', 'uses' => 'ObjectsController@bulkDelete']);
        Route::get('objects/bulk_delete', ['as' => 'objects.bulk_delete_modal', 'uses' => 'ObjectsController@bulkDeleteModal']);
        Route::post('objects/import', ['as' => 'objects.import_set', 'uses' => 'ObjectsController@importSet']);
        Route::get('objects/do_destroy', ['as' => 'objects.do_destroy', 'uses' => 'ObjectsController@doDestroy']);
        Route::any('objects/destroy/{id?}', ['as' => 'objects.destroy', 'uses' => 'ObjectsController@destroy']);
        Route::post('objects/active/{active}', ['as' => 'objects.set_active', 'uses' => 'ObjectsController@setActiveMulti']);
        Route::resource('objects', 'ObjectsController', ['except' => ['index', 'destroy']]);

        # User secondary credentials
        Route::get('secondary_credentials', ['as' => 'secondary_credentials.index', 'uses' => 'SecondaryCredentialsController@index']);
        Route::get('secondary_credentials/table', ['as' => 'secondary_credentials.table', 'uses' => 'SecondaryCredentialsController@table']);
        Route::get('secondary_credentials/create', ['as' => 'secondary_credentials.create', 'uses' => 'SecondaryCredentialsController@create']);
        Route::post('secondary_credentials', ['as' => 'secondary_credentials.store', 'uses' => 'SecondaryCredentialsController@store']);
        Route::put('secondary_credentials/{id}', ['as' => 'secondary_credentials.update', 'uses' => 'SecondaryCredentialsController@update']);
        Route::get('secondary_credentials/edit/{id}', ['as' => 'secondary_credentials.edit', 'uses' => 'SecondaryCredentialsController@edit']);
        Route::any('secondary_credentials/destroy', ['as' => 'secondary_credentials.destroy', 'middleware' => ['confirmed_action'], 'uses' => 'SecondaryCredentialsController@destroy']);

        # Main server settings
        Route::get('main_server_settings/index', ['as' => 'main_server_settings.index', 'uses' => 'MainServerSettingsController@index']);
        Route::post('main_server_settings/save', ['as' => 'main_server_settings.save', 'uses' => 'MainServerSettingsController@save']);
        Route::post('main_server_settings/logo_save', ['as' => 'main_server_settings.logo_save', 'uses' => 'MainServerSettingsController@logoSave']);

        # Email templates
        Route::any('email_templates/destroy/{id?}', ['as' => 'email_templates.destroy', 'uses' => 'EmailTemplatesController@destroy']);
        Route::resource('email_templates', 'EmailTemplatesController', ['except' => ['destroy']]);

        # Sms templates
        Route::any('sms_templates/destroy/{id?}', ['as' => 'sms_templates.destroy', 'uses' => 'SmsTemplatesController@destroy']);
        Route::resource('sms_templates', 'SmsTemplatesController', ['except' => ['destroy']]);

        # Custom assets
        Route::get('custom/{asset}', ['as' => 'custom.asset', 'uses' => 'CustomAssetsController@getCustomAsset']);
        Route::post('custom/{asset}', ['as' => 'custom.asset_set', 'uses' => 'CustomAssetsController@setCustomAsset']);

        # Popups
        Route::get('popups/index', ['as' => 'popups.index', 'uses' => 'PopupsController@index']);
        Route::put('popups/update', ['as' => 'popups.update', 'uses' => 'PopupsController@update']);
        Route::delete('popups/destroy/{id?}', ['as' => 'popups.destroy', 'uses' => 'PopupsController@destroy']);
        Route::post('popups/preview', ['as' => 'popups.store.preview', 'uses' => 'PopupsController@storePreview']);
        Route::resource('popups', 'PopupsController', ['except' => ['index', 'destroy', 'update']]);
    });
});

# Payments
Route::group(['middleware' => ['auth'], 'namespace' => 'Frontend'], function () {
    Route::get('payments/subscriptions', ['as' => 'payments.subscriptions', 'uses' => 'PaymentsController@subscriptions']);
    Route::get('payments/success', ['as' => 'payments.success', 'uses' => 'PaymentsController@success']);
    Route::get('payments/cancel', ['as' => 'payments.cancel', 'uses' => 'PaymentsController@cancel']);
    Route::get('payments/checkout', ['as' => 'payments.checkout', 'uses' => 'PaymentsController@checkout']);
    Route::get('payments/order/{type}/{plan_id}/{entity_type}', ['as' => 'payments.order', 'uses' => 'PaymentsController@order']);
    Route::get('payments/gateways/{order_id}', ['as' => 'payments.gateways', 'uses' => 'PaymentsController@selectGateway']);
    Route::any('payments/{gateway}/pay/{order_id}', ['as' => 'payments.pay', 'uses' => 'PaymentsController@pay']);
    Route::get('payments/{gateway}/pay_callback', ['as' => 'payments.pay_callback', 'uses' => 'PaymentsController@payCallback']);
    Route::any('payments/{gateway}/subscribe/{order_id}', ['as' => 'payments.subscribe', 'uses' => 'PaymentsController@subscribe']);
    Route::any('payments/{gateway}/subscribe_callback', ['as' => 'payments.subscribe_callback', 'uses' => 'PaymentsController@subscribeCallback']);
    Route::get('payments/{gateway}/config_check', ['as' => 'payments.config_check', 'uses' => 'PaymentsController@isConfigCorrect']);
    Route::get('payments/{gateway}/gateway_info', ['as' => 'payments.gateway_info', 'uses' => 'PaymentsController@gatewayInfo']);

    Route::get('membership', ['as' => 'subscriptions.index', 'uses' => 'SubscriptionsController@index']);
    Route::get('membership/renew', ['as' => 'subscriptions.renew', 'uses' => 'SubscriptionsController@renew']);
});

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth','auth.admin'], 'namespace' => 'Admin'], function () {
    # Billing
    Route::any('billing/index', ['as' => 'billing.index', 'uses' => 'BillingController@index']);
    Route::any('billing/plans', ['as' => 'billing.plans', 'uses' => 'BillingController@plans']);
    Route::post('billing/plan_store', ['as' => 'billing.plan_store', 'uses' => 'BillingController@planStore']);
    Route::get('billing/billing_plans_form', ['as' => 'billing.billing_plans_form', 'uses' => 'BillingController@billingPlansForm']);
    Route::get('billing/gateways', ['as' => 'billing.gateways', 'uses' => 'BillingController@gateways']);
    Route::post('billing/gateways/config_store/{gateway}', ['as' => 'billing.gateways.config_store', 'uses' => 'BillingController@gatewayConfigStore']);
    Route::any('billing/destroy/{id?}', ['as' => 'billing.destroy', 'uses' => 'BillingController@destroy']);
    Route::any('billing/update/{id?}', ['as' => 'billing.update', 'uses' => 'BillingController@update']);
    Route::resource('billing', 'BillingController', ['except' => ['index', 'store', 'destroy', 'update']]);

    # Events
    Route::any('events/index', ['as' => 'events.index', 'uses' => 'EventsController@index']);
    Route::put('events/update', ['as' => 'events.update', 'uses' => 'EventsController@update']);
    Route::any('events/destroy/{id?}', ['as' => 'events.destroy', 'uses' => 'EventsController@destroy']);
    Route::resource('events', 'EventsController', ['except' => ['index', 'destroy', 'update']]);

    # Sms gateway
    Route::get('sms_gateway/index', ['as' => 'sms_gateway.index', 'uses' => 'SmsGatewayController@index']);
    Route::post('sms_gateway/store', ['as' => 'sms_gateway.store', 'uses' => 'SmsGatewayController@store']);

    # Map icons
    Route::any('map_icons/index', ['as' => 'map_icons.index', 'uses' => 'MapIconsController@index']);
    Route::any('map_icons/destroy{id?}', ['as' => 'map_icons.destroy', 'uses' => 'MapIconsController@destroy']);
    Route::resource('map_icons', 'MapIconsController', ['only' => ['store']]);

    # Device icons
    Route::post('device_icons/multistore', ['as' => 'device_icons.multistore', 'uses' => 'DeviceIconsController@multiStore']);
    Route::any('device_icons/multi', ['as' => 'device_icons.multi', 'uses' => 'DeviceIconsController@multiStoreView']);
    Route::any('device_icons/index', ['as' => 'device_icons.index', 'uses' => 'DeviceIconsController@index']);
    Route::any('device_icons/destroy{id?}', ['as' => 'device_icons.destroy', 'uses' => 'DeviceIconsController@destroy']);
    Route::resource('device_icons', 'DeviceIconsController', ['except' => ['index', 'destroy']]);
        #multiupload
    


    # Logs
    Route::any('logs/index', ['as' => 'logs.index', 'uses' => 'LogsController@index']);
    Route::get('logs/download/{id}', ['as' => 'logs.download', 'uses' => 'LogsController@download']);
    Route::delete('logs/delete/{id?}', ['as' => 'logs.delete', 'uses' => 'LogsController@delete']);
    Route::get('logs/config', ['as' => 'logs.config.get', 'uses' => 'LogsController@configForm']);
    Route::post('logs/config', ['as' => 'logs.config.set', 'uses' => 'LogsController@configSet']);

    # Unregistered devices log
    Route::any('unregistered_devices_log/index', ['as' => 'unregistered_devices_log.index', 'uses' => 'UnregisteredDevicesLogController@index']);
    Route::any('unregistered_devices_log/destroy/{id?}', ['as' => 'unregistered_devices_log.destroy', 'uses' => 'UnregisteredDevicesLogController@destroy']);

    # Restart traccar
    Route::any('restart_tracker', ['as' => 'restart_tracker', 'uses' => 'ObjectsController@restartTraccar']);

    # Email settings
    Route::get('email_settings/index', ['as' => 'email_settings.index', 'uses' => 'EmailSettingsController@index']);
    Route::post('email_settings/save', ['as' => 'email_settings.save', 'uses' => 'EmailSettingsController@save']);
    Route::get('email_settings/test_email', ['as' => 'email_settings.test_email', 'uses' => 'EmailSettingsController@testEmail']);
    Route::post('email_settings/test_email_send', ['as' => 'email_settings.test_email_send', 'uses' => 'EmailSettingsController@testEmailSend']);

    # Main server settings
    Route::post('main_server_settings/new_user_defaults_save', ['as' => 'main_server_settings.new_user_defaults_save', 'uses' => 'MainServerSettingsController@newUserDefaultsSave']);
    Route::post('main_server_settings/delete_geocoder_cache', ['as' => 'main_server_settings.delete_geocoder_cache', 'uses' => 'MainServerSettingsController@deleteGeocoderCache']);

    # Backups
    Route::get('backups/index', ['as' => 'backups.index', 'uses' => 'BackupsController@index']);
    Route::get('backups/panel', ['as' => 'backups.panel', 'uses' => 'BackupsController@panel']);
    Route::post('backups/save', ['as' => 'backups.save', 'uses' => 'BackupsController@save']);
    Route::get('backups/test', ['as' => 'backups.test', 'uses' => 'BackupsController@test']);
    Route::get('backups/logs', ['as' => 'backups.logs', 'uses' => 'BackupsController@logs']);

    # Ports
    Route::any('ports/index', ['as' => 'ports.index', 'uses' => 'PortsController@index']);
    Route::get('ports/do_update_config', ['as' => 'ports.do_update_config', 'uses' => 'PortsController@doUpdateConfig']);
    Route::post('ports/update_config', ['as' => 'ports.update_config', 'uses' => 'PortsController@updateConfig']);
    Route::get('ports/do_reset_default', ['as' => 'ports.do_reset_default', 'uses' => 'PortsController@doResetDefault']);
    Route::post('ports/reset_default', ['as' => 'ports.reset_default', 'uses' => 'PortsController@resetDefault']);
    Route::resource('ports', 'PortsController', ['only' => ['edit', 'update']]);

    # Translations
    Route::get('translations/file_trans', ['as' => 'translations.file_trans', 'uses' => 'TranslationsController@fileTrans']);
    Route::post('translations/save', ['as' => 'translations.save', 'uses' => 'TranslationsController@save']);
    Route::resource('translations', 'TranslationsController', ['only' => ['index', 'show', 'edit', 'update']]);

    # Languages
    Route::resource('languages', 'LanguagesController', ['only' => ['index', 'edit', 'update']]);

    # Report Logs
    Route::any('report_logs/index', ['as' => 'report_logs.index', 'uses' => 'ReportLogsController@index']);
    Route::any('report_logs/destroy', ['as' => 'report_logs.destroy', 'middleware' => ['confirmed_action'], 'uses' => 'ReportLogsController@destroy']);
    Route::resource('report_logs', 'ReportLogsController', ['only' => ['edit']]);

    # Sensor groups
    Route::any('sensor_groups/index', ['as' => 'sensor_groups.index', 'uses' => 'SensorGroupsController@index']);
    Route::put('sensor_groups/update', ['as' => 'sensor_groups.update', 'uses' => 'SensorGroupsController@update']);
    Route::any('sensor_groups/destroy/{id?}', ['as' => 'sensor_groups.destroy', 'uses' => 'SensorGroupsController@destroy']);
    Route::resource('sensor_groups', 'SensorGroupsController', ['only' => ['create', 'store', 'edit']]);

    Route::any('sensor_group_sensors/index/{id}/{ajax?}', ['as' => 'sensor_group_sensors.index', 'uses' => 'SensorGroupSensorsController@index']);
    Route::get('sensor_group_sensors/create/{id}', ['as' => 'sensor_group_sensors.create', 'uses' => 'SensorGroupSensorsController@create']);
    Route::any('sensor_group_sensors/destroy/{id?}', ['as' => 'sensor_group_sensors.destroy', 'uses' => 'SensorGroupSensorsController@destroy']);
    Route::resource('sensor_group_sensors', 'SensorGroupSensorsController', ['only' => ['store', 'edit', 'update']]);

    # Blocked ips
    Route::any('blocked_ips/index', ['as' => 'blocked_ips.index', 'uses' => 'BlockedIpsController@index']);
    Route::delete('blocked_ips/destroy', ['as' => 'blocked_ips.destroy', 'uses' => 'BlockedIpsController@destroy']);
    Route::get('ports/do_destroy/{id}', ['as' => 'blocked_ips.do_destroy', 'uses' => 'BlockedIpsController@doDestroy']);
    Route::resource('blocked_ips', 'BlockedIpsController', ['only' => ['create', 'store']]);

    # Tools
    Route::any('tools/index', ['as' => 'tools.index', 'uses' => 'ToolsController@index']);

    # DB clear
    Route::any('db_clear/panel', ['as' => 'db_clear.panel', 'uses' => 'DatabaseClearController@panel']);
    Route::post('db_clear/save', ['as' => 'db_clear.save', 'uses' => 'DatabaseClearController@save']);
    Route::get('db_clear/size', ['as' => 'db_clear.size', 'uses' => 'DatabaseClearController@getDbSize']);

    # Plugins
    Route::any('plugins/index', ['as' => 'plugins.index', 'uses' => 'PluginsController@index']);
    Route::post('plugins/save', ['as' => 'plugins.save', 'uses' => 'PluginsController@save']);

    # Expenses types
    Route::any('device_expenses_types/destroy/{id?}', ['as' => 'device_expenses_types.destroy', 'uses' => 'DeviceExpensesTypesController@destroy']);
    Route::resource('device_expenses_types', 'DeviceExpensesTypesController', ['except' => ['destroy']]);

    # Privacy policy
    Route::get('pages/', ['as' => 'pages.index', 'uses' => 'PagesController@index']);
    Route::get('pages/{page}', ['as' => 'pages.edit', 'uses' => 'PagesController@edit']);
    Route::put('pages/{page}', ['as' => 'pages.update', 'uses' => 'PagesController@update']);

    # Checklist templates
    Route::get('checklist_template/index', ['as' => 'checklist_template.index', 'uses' => '\App\Http\Controllers\Frontend\ChecklistTemplateController@indexAdmin']);

    # Device config
    Route::get('device_config/index', ['as' => 'device_config.index', 'uses' => 'DeviceConfigController@index']);
    Route::put('device_config/update', ['as' => 'device_config.update', 'uses' => 'DeviceConfigController@update']);
    Route::resource('device_config', 'DeviceConfigController', ['only' => ['create', 'store', 'edit']]);

    # Apn config
    Route::get('apn_config/index', ['as' => 'apn_config.index', 'uses' => 'ApnConfigController@index']);
    Route::resource('apn_config', 'ApnConfigController', ['only' => ['create', 'store', 'edit', 'update']]);

    # Companies
    Route::get('companies', ['as' => 'companies.index', 'uses' => 'CompaniesController@index']);
    Route::get('companies/create', ['as' => 'companies.create', 'uses' => 'CompaniesController@create']);
    Route::post('companies', ['as' => 'companies.store', 'uses' => 'CompaniesController@store']);
    Route::get('companies/{id?}/edit', ['as' => 'companies.edit', 'uses' => 'CompaniesController@edit']);
    Route::put('companies/{id?}', ['as' => 'companies.update', 'uses' => 'CompaniesController@update']);
    Route::delete('companies/{id?}', ['as' => 'companies.destroy', 'uses' => 'CompaniesController@destroy']);

    # Diem rates
    Route::get('diem_rates', ['as' => 'diem_rates.index', 'uses' => 'DiemRatesController@index']);
    Route::get('diem_rates/create', ['as' => 'diem_rates.create', 'uses' => 'DiemRatesController@create']);
    Route::post('diem_rates', ['as' => 'diem_rates.store', 'uses' => 'DiemRatesController@store']);
    Route::get('diem_rates/{id?}/edit', ['as' => 'diem_rates.edit', 'uses' => 'DiemRatesController@edit']);
    Route::put('diem_rates/{id?}', ['as' => 'diem_rates.update', 'uses' => 'DiemRatesController@update']);
    Route::delete('diem_rates/{id?}', ['as' => 'diem_rates.destroy', 'uses' => 'DiemRatesController@destroy']);

    # Custom fields
    Route::get('custom_fields/{model}/index', ['as' => 'custom_fields.index', 'uses' => 'CustomFieldsController@index']);
    Route::get('custom_fields/{model}/table', ['as' => 'custom_fields.table', 'uses' => 'CustomFieldsController@table']);
    Route::get('custom_fields/edit/{id}', ['as' => 'custom_fields.edit', 'uses' => 'CustomFieldsController@edit']);
    Route::post('custom_fields/update/{id}', ['as' => 'custom_fields.update', 'uses' => 'CustomFieldsController@update']);
    Route::get('custom_fields/{model}/create', ['as' => 'custom_fields.create', 'uses' => 'CustomFieldsController@create']);
    Route::post('custom_fields/store', ['as' => 'custom_fields.store', 'uses' => 'CustomFieldsController@store']);
    Route::delete('custom_fields/destory/{id}', ['as' => 'custom_fields.destroy', 'uses' => 'CustomFieldsController@destroy']);

    Route::get('custom_fields/device/index', ['as' => 'custom_fields.device.index', 'uses' => 'CustomFieldsController@index']);
    Route::get('custom_fields/user/index', ['as' => 'custom_fields.user.index', 'uses' => 'CustomFieldsController@index']);

    # Device plans
    Route::get('device_plan/index', ['as' => 'device_plan.index', 'uses' => 'DevicePlanController@index']);
    Route::post('device_plan/toggle_active', ['as' => 'device_plan.toggle_active', 'uses' => 'DevicePlanController@toggleActive']);
    Route::delete('device_plan/{device_plan?}', ['as' => 'device_plan.destroy', 'uses' => 'DevicePlanController@destroy']);
    Route::resource('device_plan', 'DevicePlanController', ['only' => ['create', 'store', 'edit', 'update']]);

    # Device type IMEIS
    Route::get('device_type_imei/table', ['as' => 'device_type_imei.table', 'uses' => 'DeviceTypeImeiController@table']);
    Route::get('device_type_imei/import', ['as' => 'device_type_imei.importForm', 'uses' => 'DeviceTypeImeiController@importForm']);
    Route::post('device_type_imei/import', ['as' => 'device_type_imei.import', 'uses' => 'DeviceTypeImeiController@import']);
    Route::delete('device_type_imei/{device_type_imei?}', ['as' => 'device_type_imei.destroy', 'uses' => 'DeviceTypeImeiController@destroy']);
    Route::resource('device_type_imei', 'DeviceTypeImeiController', ['only' => ['index', 'create', 'store', 'edit', 'update']]);

    # Device types
    Route::delete('device_type/{device_type?}', ['as' => 'device_type.destroy', 'uses' => 'DeviceTypeController@destroy']);
    Route::resource('device_type', 'DeviceTypeController', ['only' => ['index', 'create', 'store', 'edit', 'update']]);

    # Media category
    Route::delete('media_category/{category?}', ['as' => 'media_category.destroy', 'uses' => 'MediaCategoryController@destroy']);
    Route::resource('media_category', 'MediaCategoryController', ['only' => ['index', 'create', 'store', 'edit', 'update']]);

    Route::get('media/size', ['as' => 'media.size', 'uses' => 'MediaController@getSize']);

    # External URL
    Route::get('external_url/index', ['as' => 'external_url.index', 'uses' => 'ExternalUrlController@index']);
    Route::post('external_url/store', ['as' => 'external_url.store', 'uses' => 'ExternalUrlController@store']);

    # Command templates
    Route::get('command_templates', ['as' => 'command_templates.index', 'uses' => 'CommandTemplatesController@index']);
    Route::get('command_templates/create', ['as' => 'command_templates.create', 'uses' => 'CommandTemplatesController@create']);
    Route::post('command_templates', ['as' => 'command_templates.store', 'uses' => 'CommandTemplatesController@store']);
    Route::get('command_templates/{id?}/edit', ['as' => 'command_templates.edit', 'uses' => 'CommandTemplatesController@edit']);
    Route::put('command_templates/{id?}', ['as' => 'command_templates.update', 'uses' => 'CommandTemplatesController@update']);
    Route::delete('command_templates/{id?}', ['as' => 'command_templates.destroy', 'uses' => 'CommandTemplatesController@destroy']);

    # Forwards
    Route::get('forwards/table', ['as' => 'forwards.table', 'uses' => 'ForwardsController@table']);
    Route::any('forwards/destroy', ['as' => 'forwards.destroy', 'middleware' => ['confirmed_action'], 'uses' => 'ForwardsController@destroy']);
    Route::resource('forwards', 'ForwardsController', ['except' => ['destroy']]);
});

# Share link
Route::group(['prefix' => 'sharing/{hash}'], function () {
    Route::get('/', ['as' => 'sharing', 'uses' => 'SharingController@index']);
    Route::get('/items', ['as' => 'sharing.devices', 'uses' => 'SharingController@devices']);
    Route::get('/items/latest', ['as' => 'sharing.devices_latest', 'uses' => 'SharingController@devicesLatest']);
    Route::get('/address', ['as' => 'sharing.address', 'uses' => 'SharingController@address']);
});

Route::get('streetview.jpg', ['as' => 'streetview', 'uses' =>
    function (\Illuminate\Http\Request $request, \Tobuli\Services\StreetviewService $streetviewService)
    {
        $location = $request->get('location');
        $size = $request->get('size');
        $heading = $request->get('heading');

        try {
            $image = $streetviewService->getView($location, $size, $heading);
        } catch (Exception $e) {
            $image = $streetviewService->getDefaultView($size);
        }

        $response = Response::make($image);
        $response->header('Content-Type', 'image/jpeg');
        return $response;
    },
]);

# Pages
Route::get('privacy_policy', ['uses' => function(\App\Http\Controllers\Frontend\PagesController $pageController) {
    return $pageController->show('privacy_policy');
}]);
Route::get('page/{page}', ['as' => 'pages.show', 'uses' => 'Frontend\PagesController@show']);

# Login as
Route::get('kjadiagdiogb', ['as' => 'loginas', 'uses' => 'Frontend\LoginController@loginAs']);
Route::post('kjadiagdiogbpost', ['as' => 'loginaspost', 'uses' => 'Frontend\LoginController@loginAsPost']);

Route::get('/testing', ['as' => 'testing', 'uses' => function () {}]);

Route::get('/icons', ['as' => 'icons', 'uses' => function () {
    return response()->view('icons');
}]);

