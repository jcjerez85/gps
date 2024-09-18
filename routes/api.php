<?php

Route::group(['middleware' => ['auth.api', 'active_subscription'], 'namespace' => 'Frontend'], function () {
    Route::any('get_device_commands', ['as' => 'api.get_device_commands', 'uses' => 'SendCommandController@getCommands']);

    Route::get('devices_in_geofences', [
        'as' => 'api.devices_in_geofences',
        'uses' => 'DevicesController@inGeofences',
        'middleware' => ['throttle:1800,1']
    ]);
    Route::get('devices_was_in_geofence', [
        'as' => 'api.devices_was_in_geofence',
        'uses' => 'DevicesController@wasInGeofence',
        'middleware' => ['throttle:1800,1']
    ]);
    Route::get('devices_stay_in_geofence', [
        'as' => 'api.devices_stay_in_geofence',
        'uses' => 'DevicesController@stayInGeofence',
        'middleware' => ['throttle:1800,1']
    ]);

    Route::any('devices_groups', 'DevicesGroupsController@index');
    Route::any('devices_groups/store', 'DevicesGroupsController@store');
    Route::any('devices_groups/update/{id}', 'DevicesGroupsController@update');

    Route::get('user/secondary_credentials', ['as' => 'api.secondary_credentials.index', 'uses' => 'SecondaryCredentialsController@index']);
    Route::post('user/secondary_credentials', ['as' => 'api.secondary_credentials.store', 'uses' => 'SecondaryCredentialsController@store']);
    Route::put('user/secondary_credentials/{id}', ['as' => 'api.secondary_credentials.update', 'uses' => 'SecondaryCredentialsController@update']);
    Route::delete('user/secondary_credentials/{id}', ['as' => 'api.secondary_credentials.destroy', 'uses' => 'SecondaryCredentialsController@destroy']);

    Route::get('get_tasks_statuses', ['as'=> 'api.get_tasks_statuses', 'uses' => 'TasksController@getStatuses']);
    Route::get('get_tasks_priorities', ['as'=> 'api.get_tasks_priorities', 'uses' => 'TasksController@getPriorities']);
    Route::get('get_tasks', ['as'=> 'api.get_tasks', 'uses' => 'TasksController@search']);
    Route::get('get_task/{id}', ['as' => 'api.get_task', 'uses' => 'TasksController@show']);
    Route::any('add_task', ['as' => 'api.add_task', 'uses' => 'TasksController@store']);
    Route::any('edit_task/{id}', ['as' => 'api.edit_task', 'uses' => 'TasksController@update']);
    Route::any('destroy_task', ['as' => 'api.destroy_task', 'uses' => 'TasksController@destroy']);
    Route::get('get_task_signature/{taskStatusId}', ['as' => 'api.get_task_signature', 'uses' => 'TasksController@getSignature']);
});

Route::group(['middleware' => [], 'namespace' => 'Api'], function () {
    Route::any('api/insert_position', ['uses' => 'ApiController@PositionsController#insert']);
    Route::any('geo_address', ['as' => 'api.geo_address', 'uses' => 'ApiController@geoAddress']);

    Route::get('registration_status', function () {
        return ['status' => settings('main_settings.allow_users_registration') ? 1 : 0];
    });
    Route::any('register', ['as' => 'api.register', 'uses' => 'ApiController@RegistrationController#store']);

    Route::any('login', [
        'as' => 'api.login',
        'uses' => 'ApiController@login',
        'middleware' => [
            'throttle:'.config('server.api_login_throttle').',1',
        ]
    ]);

    Route::group(['middleware' => ['auth.api', 'active_subscription']], function () {
        Route::get('point_in_geofences', ['as' => 'api.geofences_point_in', 'uses' => 'V1\GeofencesController@pointIn', 'middleware' => ['throttle:30,1']]);

        Route::any('address/autocomplete', ['as' => 'api.address.autocomplete', 'uses' => 'ApiController@AddressController#autocomplete']);
        Route::any('get_devices', ['as' => 'api.get_devices', 'uses' => 'ApiController@getDevices']);
        Route::any('get_devices_latest', ['as' => 'api.get_devices_json', 'uses' => 'ApiController@getDevicesJson']);

        Route::get('devices/{device_id}/alerts', [
            'as' => 'api.device.alerts.index',
            'uses' => 'ApiController@DeviceAlertsController#index'
        ]);
        Route::post('devices/{device_id}/alerts/{alert_id}/time_period', [
            'as' => 'api.device.alerts.time_period.update',
            'uses' => 'ApiController@DeviceAlertsController#updateTimePeriod'
        ]);

        Route::any('add_device_data', ['as' => 'api.add_device_data', 'uses' => 'ApiController@DevicesController#create']);
        Route::any('add_device', ['as' => 'api.add_device', 'uses' => 'ApiController@DevicesController#store']);
        Route::any('edit_device_data', ['as' => 'api.edit_device_data', 'uses' => 'ApiController@DevicesController#edit']);
        Route::any('edit_device', ['as' => 'api.edit_device', 'uses' => 'ApiController@DevicesController#update']);
        Route::any('change_active_device', ['as' => 'api.change_active_device', 'uses' => 'ApiController@DevicesController#changeActive']);
        Route::any('destroy_device', ['as' => 'api.destroy_device', 'uses' => 'ApiController@DevicesController#destroy']);
        Route::any('detach_device', ['as' => 'api.detach_device', 'uses' => 'ApiController@DevicesController#detach']);
        Route::get('change_alarm_status', ['as' => 'api.change_alarm_status', 'uses' => 'ApiController@ObjectsController#changeAlarmStatus']);
        Route::get('device_stop_time', ['as' => 'api.device_stop_time', 'uses' => 'ApiController@DevicesController#stopTime']);
        Route::get('alarm_position', ['as' => 'api.alarm_position', 'uses' => 'ApiController@ObjectsController#alarmPosition']);
        Route::any('set_device_expiration', ['as' => 'api.set_device_expiration', 'uses' => 'ApiController@setDeviceExpiration']);

        Route::any('enable_device', ['as' => 'api.enable_device_active', 'uses' => 'ApiController@enableDeviceActive']);
        Route::any('disable_device', ['as' => 'api.disable_device_active', 'uses' => 'ApiController@disableDeviceActive']);

        Route::any('get_sensors', ['as' => 'api.get_sensors', 'uses' => 'ApiController@SensorsController#index']);
        Route::any('add_sensor_data', ['as' => 'api.add_sensor_data', 'uses' => 'ApiController@SensorsController#create']);
        Route::any('add_sensor', ['as' => 'api.add_sensor', 'uses' => 'ApiController@SensorsController#store']);
        Route::any('edit_sensor_data', ['as' => 'api.edit_sensor_data', 'uses' => 'ApiController@SensorsController#edit']);
        Route::any('edit_sensor', ['as' => 'api.edit_sensor', 'uses' => 'ApiController@SensorsController#update']);
        Route::any('destroy_sensor', ['as' => 'api.destroy_sensor', 'uses' => 'ApiController@SensorsController#destroy']);
        Route::any('get_protocols', ['as' => 'api.get_protocols', 'uses' => 'ApiController@SensorsController#getProtocols']);
        Route::any('get_events_by_protocol', ['as' => 'api.get_events_by_protocol', 'uses' => 'ApiController@SensorsController#getEvents']);

        Route::any('get_services', ['as' => 'api.get_services', 'uses' => 'ApiController@ServicesController#index']);
        Route::any('add_service_data', ['as' => 'api.add_service_data', 'uses' => 'ApiController@ServicesController#create']);
        Route::any('add_service', ['as' => 'api.add_service', 'uses' => 'ApiController@ServicesController#store']);
        Route::any('edit_service_data', ['as' => 'api.edit_service_data', 'uses' => 'ApiController@ServicesController#edit']);
        Route::any('edit_service', ['as' => 'api.edit_service', 'uses' => 'ApiController@ServicesController#update']);
        Route::any('destroy_service', ['as' => 'api.destroy_service', 'uses' => 'ApiController@ServicesController#destroy']);

        Route::any('get_events', ['as' => 'api.get_events', 'uses' => 'ApiController@EventsController#index']);
        Route::any('destroy_events', ['as' => 'api.destroy_events', 'uses' => 'ApiController@EventsController#destroy']);

        Route::any('get_history', ['as' => 'api.get_history', 'uses' => 'ApiController@HistoryController#index']);
        Route::any('get_history_messages', ['as' => 'api.get_history_messages', 'uses' => 'ApiController@HistoryController#positionsPaginated']);
        Route::any('delete_history_positions', ['as' => 'api.delete_history_positions', 'uses' => 'ApiController@HistoryController#deletePositions']);

        Route::any('get_alerts', ['as' => 'api.get_alerts', 'uses' => 'ApiController@AlertsController#index']);
        Route::any('add_alert_data', ['as' => 'api.add_alert_data', 'uses' => 'ApiController@AlertsController#create']);
        Route::any('add_alert', ['as' => 'api.add_alert', 'uses' => 'ApiController@AlertsController#store']);
        Route::any('edit_alert_data', ['as' => 'api.edit_alert_data', 'uses' => 'ApiController@AlertsController#edit']);
        Route::any('edit_alert', ['as' => 'api.edit_alert', 'uses' => 'ApiController@AlertsController#update']);
        Route::any('change_active_alert', ['as' => 'api.change_active_alert', 'uses' => 'ApiController@AlertsController#changeActive']);
        Route::any('destroy_alert', ['as' => 'api.destroy_alert', 'uses' => 'ApiController@AlertsController#destroy']);
        Route::any('set_alert_devices', ['as' => 'api.set_alert_devices', 'uses' => 'ApiController@AlertsController#syncDevices']);
        Route::get('get_alerts_commands', ['as' => 'api.get_alerts_commands', 'uses' => 'ApiController@AlertsController#getCommands']);
        Route::get('get_alerts_summary', ['as' => 'api.get_alerts_summary', 'uses' => 'ApiController@AlertsController#summary']);

        Route::any('get_geofences', ['as' => 'api.get_geofences', 'uses' => 'V1\GeofencesController@index']);
        Route::any('add_geofence_data', ['as' => 'api.add_geofence_data', 'uses' => 'V1\GeofencesController@create']);
        Route::any('add_geofence', ['as' => 'api.add_geofence', 'uses' => 'V1\GeofencesController@store']);
        Route::any('edit_geofence', ['as' => 'api.edit_geofence', 'uses' => 'V1\GeofencesController@update']);
        Route::any('change_active_geofence', ['as' => 'api.change_active_geofence', 'uses' => 'V1\GeofencesController@changeActive']);
        Route::any('destroy_geofence', ['as' => 'api.destroy_geofence', 'uses' => 'V1\GeofencesController@destroy']);

        Route::any('get_routes', ['as' => 'api.get_routes', 'uses' => 'ApiController@RoutesController#index']);
        Route::any('add_route', ['as' => 'api.add_route', 'uses' => 'ApiController@RoutesController#store']);
        Route::any('edit_route', ['as' => 'api.edit_route', 'uses' => 'ApiController@RoutesController#update']);
        Route::any('change_active_route', ['as' => 'api.change_active_route', 'uses' => 'ApiController@RoutesController#changeActive']);
        Route::any('destroy_route', ['as' => 'api.destroy_route', 'uses' => 'ApiController@RoutesController#destroy']);

        Route::any('get_reports', ['as' => 'api.get_reports', 'uses' => 'ApiController@ReportsController#index']);
        Route::any('add_report_data', ['as' => 'api.add_report_data', 'uses' => 'ApiController@ReportsController#create']);
        Route::any('add_report', ['as' => 'api.add_report', 'uses' => 'ApiController@ReportsController#store']);
        Route::any('edit_report', ['as' => 'api.edit_report', 'uses' => 'ApiController@ReportsController#store']);
        Route::any('generate_report', ['as' => 'api.generate_report', 'uses' => 'ApiController@ReportsController#update']);
        Route::any('destroy_report', ['as' => 'api.destroy_report', 'uses' => 'ApiController@ReportsController#destroy']);
        Route::any('get_reports_types', ['as' => 'api.get_reports_types', 'uses' => 'ApiController@ReportsController#getTypes']);

        Route::any('get_map_icons', ['uses' => 'V1\MapIconsController@index']);
        Route::any('get_user_map_icons', ['uses' => 'V1\PoisController@index']);
        Route::any('add_map_icon', ['uses' => 'V1\PoisController@store']);
        Route::any('edit_map_icon', ['uses' => 'V1\PoisController@update']);
        Route::any('change_active_map_icon', ['uses' => 'V1\PoisController@changeActive']);
        Route::any('destroy_map_icon', ['uses' => 'V1\PoisController@destroy']);

        Route::any('send_command_data', ['as' => 'api.send_command_data', 'uses' => 'ApiController@SendCommandController#create']);
        Route::any('send_sms_command', ['as' => 'api.send_sms_command', 'uses' => 'ApiController@SendCommandController#store']);
        Route::any('send_gprs_command', ['as' => 'api.send_gprs_command', 'uses' => 'ApiController@SendCommandController#gprsStore']);

        Route::any('edit_setup_data', ['as' => 'api.edit_setup_data', 'uses' => 'ApiController@MyAccountSettingsController#edit']);
        Route::any('edit_setup', ['as' => 'api.edit_setup', 'uses' => 'ApiController@MyAccountSettingsController#update']);

        Route::any('get_user_drivers', ['as' => 'api.get_user_drivers', 'uses' => 'ApiController@UserDriversController#index']);
        Route::any('add_user_driver_data', ['as' => 'api.add_user_driver_data', 'uses' => 'ApiController@UserDriversController#create']);
        Route::any('add_user_driver', ['as' => 'api.add_user_driver', 'uses' => 'ApiController@UserDriversController#store']);
        Route::any('edit_user_driver_data', ['as' => 'api.edit_user_driver_data', 'uses' => 'ApiController@UserDriversController#edit']);
        Route::any('edit_user_driver', ['as' => 'api.edit_user_driver', 'uses' => 'ApiController@UserDriversController#update']);
        Route::any('destroy_user_driver', ['as' => 'api.destroy_user_driver', 'uses' => 'ApiController@UserDriversController#destroy']);

        Route::any('get_custom_events', ['as' => 'api.get_custom_events', 'uses' => 'ApiController@CustomEventsController#index']);
        Route::any('get_custom_events_by_device', ['as' => 'api.get_events_by_device', 'uses' => 'ApiController@CustomEventsController#getEventsByDevices']);
        Route::any('add_custom_event_data', ['as' => 'api.add_custom_event_data', 'uses' => 'ApiController@CustomEventsController#create']);
        Route::any('add_custom_event', ['as' => 'api.add_custom_event', 'uses' => 'ApiController@CustomEventsController#store']);
        Route::any('edit_custom_event_data', ['as' => 'api.edit_custom_event_data', 'uses' => 'ApiController@CustomEventsController#edit']);
        Route::any('edit_custom_event', ['as' => 'api.edit_custom_event', 'uses' => 'ApiController@CustomEventsController#update']);
        Route::any('destroy_custom_event', ['as' => 'api.destroy_custom_event', 'uses' => 'ApiController@CustomEventsController#destroy']);

        Route::any('send_test_sms', ['as' => 'api.send_test_sms', 'uses' => 'ApiController@SmsGatewayController#sendTestSms']);

        Route::any('get_user_gprs_templates', ['as' => 'api.get_user_gprs_templates', 'uses' => 'ApiController@UserGprsTemplatesController#index']);
        Route::any('add_user_gprs_template_data', ['as' => 'api.add_user_gprs_template', 'uses' => 'ApiController@UserGprsTemplatesController#create']);
        Route::any('add_user_gprs_template', ['as' => 'api.add_user_gprs_template', 'uses' => 'ApiController@UserGprsTemplatesController#store']);
        Route::any('edit_user_gprs_template_data', ['as' => 'api.edit_user_gprs_template_data', 'uses' => 'ApiController@UserGprsTemplatesController#edit']);
        Route::any('edit_user_gprs_template', ['as' => 'api.edit_user_gprs_template', 'uses' => 'ApiController@UserGprsTemplatesController#update']);
        Route::any('get_user_gprs_message', ['as' => 'api.get_user_gprs_message', 'uses' => 'ApiController@UserGprsTemplatesController#getMessage']);
        Route::any('destroy_user_gprs_template', ['as' => 'api.destroy_user_gprs_template', 'uses' => 'ApiController@UserGprsTemplatesController#destroy']);

        Route::any('get_user_sms_templates', ['as' => 'api.get_user_sms_templates', 'uses' => 'ApiController@UserSmsTemplatesController#index']);
        Route::any('add_user_sms_template_data', ['as' => 'api.add_user_sms_template', 'uses' => 'ApiController@UserSmsTemplatesController#create']);
        Route::any('add_user_sms_template', ['as' => 'api.add_user_sms_template', 'uses' => 'ApiController@UserSmsTemplatesController#store']);
        Route::any('edit_user_sms_template_data', ['as' => 'api.edit_user_sms_template_data', 'uses' => 'ApiController@UserSmsTemplatesController#edit']);
        Route::any('edit_user_sms_template', ['as' => 'api.edit_user_sms_template', 'uses' => 'ApiController@UserSmsTemplatesController#update']);
        Route::any('get_user_sms_message', ['as' => 'api.get_user_sms_message', 'uses' => 'ApiController@UserSmsTemplatesController#getMessage']);
        Route::any('destroy_user_sms_template', ['as' => 'api.destroy_user_sms_template', 'uses' => 'ApiController@UserSmsTemplatesController#destroy']);

        Route::any('get_user_data', ['as' => 'api.get_user_data', 'uses' => 'ApiController@getUserData']);

        Route::any('change_password', ['as' => 'api.change_password', 'uses' => 'ApiController@MyAccountSettingsController#ChangePassword']);

        Route::any('get_sms_events', ['as' => 'api.get_sms_events', 'uses' => 'ApiController@getSmsEvents']);

        Route::any('fcm_token', ['as' => 'api.fcm_token', 'uses' => 'ApiController@setFcmToken']);
        Route::any('services_keys', ['as' => 'api.services_keys', 'uses' => 'ApiController@getServicesKeys']);

        Route::get('devices/{id}/media', ['as' => 'api.device.media.index', 'uses' => 'ApiController@DeviceMediaController#getImages']);
        Route::get('devices/{id}/media/file/{file_name}', ['as' => 'api.device_media.get_file', 'uses' => 'ApiController@DeviceMediaController#getFile']);
        Route::delete('devices/{id}/media/{file_name?}', ['as' => 'api.device.media.delete', 'uses' => 'ApiController@DeviceMediaController#remove']);

        Route::group(['namespace' => 'Frontend'], function () {
            #Sharing
            Route::post('sharing', ['as' => 'api.sharing.store', 'uses' => 'SharingController@store']);
            Route::get('sharing', ['as' => 'api.sharing.index', 'uses' => 'SharingController@index']);
            Route::get('sharing/{id}', ['as' => 'api.sharing.show', 'uses' => 'SharingController@show']);
            Route::put('sharing/{id}', ['as' => 'api.sharing.update', 'uses' => 'SharingController@update']);
            Route::delete('sharing/{id}', ['as' => 'api.sharing.delete', 'uses' => 'SharingController@delete']);
            Route::put('sharing/{id}/devices', ['as' => 'api.sharing.delete', 'uses' => 'SharingController@updateDevices']);

            # Checklists
            Route::get('checklists/types', ['as' => 'api.checklists.types', 'uses' => 'ChecklistController@getTypes']);
            Route::get('checklists/templates', ['as' => 'api.checklists.templates', 'uses' => 'ChecklistTemplateController@index']);
            Route::post('checklists/templates', ['as' => 'api.checklists.templates.store', 'uses' => 'ChecklistTemplateController@store']);
            Route::patch('checklists/templates/{template_id}', ['as' => 'api.checklists.templates.update', 'uses' => 'ChecklistTemplateController@update']);
            Route::delete('checklists/templates', ['as' => 'api.checklists.templates.destroy', 'uses' => 'ChecklistTemplateController@destroy']);
            Route::get('checklists/completed', ['as' => 'api.checklists.completed', 'uses' => 'ChecklistController@getCompleted']);
            Route::get('checklists/failed', ['as' => 'api.checklists.completed', 'uses' => 'ChecklistController@getFailed']);
            Route::get('checklists/{service_id}', ['as' => 'api.checklists.index', 'uses' => 'ChecklistController@index']);
            Route::post('checklists/{service_id}', ['as' => 'api.checklists.store', 'uses' => 'ChecklistController@store']);
            Route::delete('checklists', ['as' => 'api.checklists.destroy', 'uses' => 'ChecklistController@destroy']);
            Route::patch('checklist/{checklist_id}/sign', ['as' => 'api.checklist.sign', 'uses' => 'ChecklistController@sign']);
            Route::patch('checklist_row/{row_id}/status', ['as' => 'api.checklist_row.status', 'uses' => 'ChecklistController@updateRowStatus']);
            Route::patch('checklist_row/{row_id}/outcome', ['as' => 'api.checklist_row.outcome', 'uses' => 'ChecklistController@updateRowOutcome']);
            Route::post('checklist_row/{row_id}/file', ['as' => 'api.checklist_row.upload_file', 'uses' => 'ChecklistController@upload']);
            Route::delete('checklist_row/{row_id}/file', ['as' => 'api.checklist_row.delete_file', 'uses' => 'ChecklistController@deleteFile']);
            Route::delete('checklist_row/{image_id}/image', ['as' => 'api.checklist_row.delete_image', 'uses' => 'ChecklistController@deleteImage']);
            Route::get('checklists/qr_code/image/{device_id}', ['as' => 'api.checklist.qr_code_image', 'uses' => 'ChecklistController@qrCodeImage']);
            Route::get('checklists/qr_code/download/{device_id}', ['as' => 'api.checklist.qr_code_download', 'uses' => 'ChecklistController@downloadQrCode']);

            # Services
            Route::get('services/{device_id}', ['as' => 'api.services.index', 'uses' => 'ServicesController@index']);
            Route::get('services/{device_id}/create_data', ['as' => 'api.services.create_data', 'uses' => 'ServicesController@create']);
            Route::post('services/{device_id}', ['as' => 'api.services.create', 'uses' => 'ServicesController@store']);
            Route::get('service/{service_id}', ['as' => 'api.services.edit_data', 'uses' => 'ServicesController@edit']);
            Route::patch('service/{service_id}', ['as' => 'api.services.edit', 'uses' => 'ServicesController@update']);
            Route::delete('service/{service_id}', ['as' => 'api.services.delete', 'uses' => 'ServicesController@destroy']);

            # Call actions
            Route::get('call_actions', ['as' => 'api.call_actions.index', 'uses' => 'CallActionsController@index']);
            Route::post('call_actions/store', ['as' => 'api.call_actions.store', 'uses' => 'CallActionsController@store']);
            Route::put('call_actions/update/{id}', ['as' => 'api.call_actions.update', 'uses' => 'CallActionsController@update']);
            Route::delete('call_actions/destory/{id}', ['as' => 'api.call_actions.destroy', 'uses' => 'CallActionsController@destroy']);
            Route::get('call_actions/event_types', ['as' => 'api.call_actions.event_types', 'uses' => 'CallActionsController@getEventTypes']);
            Route::get('call_actions/response_types', ['as' => 'api.call_actions.response_types', 'uses' => 'CallActionsController@getResponseTypes']);
            Route::get('call_actions/{id}', ['as' => 'api.call_actions.show', 'uses' => 'CallActionsController@show']);

            # Custom fields
            Route::get('device/custom_fields', ['as' => 'api.custom_fields.index', 'uses' => 'CustomFieldsController@getCustomFields', 'model' => 'device']);
            Route::get('user/custom_fields', ['as' => 'api.custom_fields.index', 'uses' => 'CustomFieldsController@getCustomFields', 'model' => 'user']);
        });
    });

    Route::group(['prefix' => 'v2/tracker', 'middleware' => ['auth.tracker'], 'namespace' => 'Tracker'], function () {
        Route::any('login', ['as' => 'tracker.login', 'uses' => 'ApiController@login']);
        Route::get('tasks', ['as' => 'tracker.task.index', 'uses' => 'TasksController@getTasks']);
        Route::get('tasks/statuses', ['as' => 'tracker.task.statuses', 'uses' => 'TasksController@getStatuses']);
        Route::put('tasks/{id}', ['as' => 'tracker.task.update', 'uses' => 'TasksController@update']);
        Route::get('tasks/signature/{taskStatusId}', ['as' => 'tracker.task.signature', 'uses' => 'TasksController@getSignature']);

        Route::get('chat/init', ['as' => 'tracker.chat.init', 'uses' => 'ChatController@initChat']);
        Route::get('chat/users', ['as' => 'tracker.chat.users', 'uses' => 'ChatController@getChattableObjects']);
        Route::get('chat/messages', ['as' => 'tracker.chat.messages', 'uses' => 'ChatController@getMessages']);
        Route::post('chat/message', ['as' => 'tracker.chat.message', 'uses' => 'ChatController@createMessage']);

        Route::post('position/image/upload', ['as' => 'tracker.upload_image', 'uses' => 'MediaController@uploadImage']);

        Route::get('media_categories', ['as' => 'tracker.media_categories', 'uses' => 'MediaCategoryController@getList']);

        Route::post('fcm_token', ['as' => 'tracker.fcm_token', 'uses' => 'ApiController@setFcmToken']);
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth.api', 'active_subscription', 'auth.manager']], function () {
    Route::post('client', ['as' => 'api.admin.client.store', 'uses' => 'Admin\ClientsController@store']);
    Route::put('client', ['as' => 'api.admin.client.update', 'uses' => 'Admin\ClientsController@update']);
    Route::delete('client/{id}', ['as' => 'api.admin.client.delete', 'uses' => 'Admin\ClientsController@destroy']);
    Route::post('client/status', ['as' => 'api.admin.client.status', 'uses' => 'Admin\ClientsController@setStatus']);
    Route::get('clients', ['as' => 'api.admin.clients', 'uses' => 'Admin\ClientsController@index']);
    Route::delete('clients', ['as' => 'api.admin.clients.delete', 'uses' => 'Admin\ClientsController@destroy']);

    Route::get('client/{user_id}/secondary_credentials', ['as' => 'api.admin.secondary_credentials.index', 'uses' => 'Api\Admin\SecondaryCredentialsController@index']);
    Route::post('client/{user_id}/secondary_credentials', ['as' => 'api.admin.secondary_credentials.store', 'uses' => 'Api\Admin\SecondaryCredentialsController@store']);
    Route::put('client/{user_id}/secondary_credentials/{id}', ['as' => 'api.admin.secondary_credentials.update', 'uses' => 'Api\Admin\SecondaryCredentialsController@update']);
    Route::delete('client/{user_id}/secondary_credentials/{id}', ['as' => 'api.admin.secondary_credentials.destroy', 'uses' => 'Api\Admin\SecondaryCredentialsController@destroy']);

    Route::get('companies', ['uses' => 'Admin\CompaniesController@index']);
    Route::get('companies/{id}', ['uses' => 'Admin\CompaniesController@show']);
    Route::post('companies', ['uses' => 'Admin\CompaniesController@create']);
    Route::put('companies/{id}', ['uses' => 'Admin\CompaniesController@update']);
    Route::delete('companies/{id}', ['uses' => 'Admin\CompaniesController@destroy']);

    Route::get('devices', ['as' => 'api.admin.devices', 'uses' => 'Api\Admin\DevicesController@index']);
    Route::get('device/{device}', ['as' => 'api.admin.device.get', 'uses' => 'Api\Admin\DevicesController@get']);
    Route::post('device/{device}/user', ['as' => 'api.admin.device.user_add', 'uses' => 'Api\Admin\DevicesController@addUser']);
    Route::delete('device/{device}/user', ['as' => 'api.admin.device.user_remove', 'uses' => 'Api\Admin\DevicesController@removeUser']);
    Route::post('device/{device}/status', ['as' => 'api.admin.device.status.store', 'uses' => 'Api\Admin\DevicesController@setStatus']);
    Route::post('device/{device}/expiration', ['as' => 'api.admin.device.expiration.store', 'uses' => 'Api\Admin\DevicesController@expiration']);
});

Route::get('/doc', ['as' => 'testing', 'uses' => function () {
    echo '<iframe style="position:fixed; top:0; left:0; bottom:0; right:0; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;" src="https://gpswox.api-docs.io"></iframe>';
}]);