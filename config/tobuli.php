<?php
return [
    'version' => '3.7.1',
    'key'  => env('key', ''),
    'type' => env('APP_TYPE', 'ss3'),
    
    'logs_path' => env('logs_path', '/opt/traccar/logs'),
    'media_path' => env('media_path', '/var/www/html/requestPhoto/'),

    'log_send_mail_template' => env('LOG_SEND_MAIL_TEMPLATE', false),

    'geocoder_cache_driver' => env('GEOCODER_CACHE_DRIVER', 'sqlite'),

    'payments_error_log' => env('PAYMENTS_ERROR_LOG', false),

    'main_settings' => [
        'server_name' => 'GPS Server',
        'server_description' => 'GPS Tracking System for Personal Use or Business',
        'available_maps' => [
            "2"  => 2,
            "51"  => 51,
            "53"  => 53,
            "54"  => 54,
            "55"  => 55,
            "70"  => 70,
            "71"  => 71
        ],
        'default_language' => 'en',
        'default_timezone' => 16,
        'default_date_format' => 'Y-m-d',
        'default_time_format' => 'H:i:s',
        'default_duration_format' => 'standart',
        'default_unit_of_distance' => 'km',
        'default_unit_of_capacity' => 'lt',
        'default_unit_of_altitude' => 'mt',
        'default_map' => 2,
        'default_object_online_timeout' => 5,
        'default_object_inactive_timeout' => 7200, //5 days in minutes
        'default_fuel_avg_per' => 'distance',
        'allow_users_registration' => 0,

        'devices_limit' => 5,
        'subscription_expiration_after_days' => 30,
        'enable_plans' => 0,
        'default_billing_plan' => '',
        'dst' => NULL,
        'dst_date_from' => '',
        'dst_date_to' => '',
        'map_center_latitude' => '51.505',
        'map_center_longitude' => '-0.09',
        'map_zoom_level' => 19,
        'user_permissions' => [],
        'geocoder_cache_enabled' => 1,
        'geocoder_cache_days' => 90,
        'geocoders' => [
            'primary' => [
                'api' => 'default',
                'api_key' => '',
            ],
        ],
        'lbs' => [
            'provider' => '',
            'api_key' => '',
        ],
        'expire_notification' => [
            'active_before' => 0,
            'active_after'  => 0,

            'days_before'   => 10,
            'days_after'    => 10,
        ],

        'streetview_api' => null,
        'streetview_key' => '',
        'device_cameras_days' => 30,

        'template_color' => 'light-blue',
        'welcome_text' => null,
        'bottom_text' => null,
        'apple_store_link' => null,
        'google_play_link' => null,
        'enable_device_plans' => 0,
        'email_verification' => 0,
    ],
    'max_speed' => env('MAX_SPEED_LIMIT', 300),
    'min_time_gap' => env('MIN_TIME_GAP', 600),
    'prev_position_device_object' => env('PREV_POSITION_DEVICE_OBJECT', false),
    'apply_network_data' => env('APPLY_NETWORK_DATA', false),

  # Minutes before device is offline
    'device_offline_minutes' => 3,
    'check_frequency' => env('APP_CHECK_FREQUENCY', 5),
    'check_chat_frequency' => env('APP_CHECK_CHAT_FREQUENCY', 5),
    'check_chat_unread_frequency' => env('APP_CHECK_CHAT_UNREAD_FREQUENCY', 60),
    #Groups
    'group_admin' => 1,
    'group_client' => 2,
    'alert_zones' => [
        '1' => 'Zone in',
        '2' => 'Zone Out'
    ],
    'alert_fuel_type' => [
        '1' => 'L',
        '2' => 'Gal'
    ],
    'alert_distance' => [
        '1' => 'km',
        '2' => 'mi'
    ],
    'history_time' => [
        '00:00' => '00:00', '00:15' => '00:15', '00:30' => '00:30', '00:45' => '00:45', '01:00' => '01:00', '01:15' => '01:15', '01:30' => '01:30', '01:45' => '01:45',
        '02:00' => '02:00', '02:15' => '02:15', '02:30' => '02:30', '02:45' => '02:45', '03:00' => '03:00', '03:15' => '03:15', '03:30' => '03:30', '03:45' => '03:45',
        '04:00' => '04:00', '04:15' => '04:15', '04:30' => '04:30', '04:45' => '04:45', '05:00' => '05:00', '05:15' => '05:15', '05:30' => '05:30', '05:45' => '05:45',
        '06:00' => '06:00', '06:15' => '06:15', '06:30' => '06:30', '06:45' => '06:45', '07:00' => '07:00', '07:15' => '07:15', '07:30' => '07:30', '07:45' => '07:45',
        '08:00' => '08:00', '08:15' => '08:15', '08:30' => '08:30', '08:45' => '08:45', '09:00' => '09:00', '09:15' => '09:15', '09:30' => '09:30', '09:45' => '09:45',
        '10:00' => '10:00', '10:15' => '10:15', '10:30' => '10:30', '10:45' => '10:45', '11:00' => '11:00', '11:15' => '11:15', '11:30' => '11:30', '11:45' => '11:45',
        '12:00' => '12:00', '12:15' => '12:15', '12:30' => '12:30', '12:45' => '12:45', '13:00' => '13:00', '13:15' => '13:15', '13:30' => '13:30', '13:45' => '13:45',
        '14:00' => '14:00', '14:15' => '14:15', '14:30' => '14:30', '14:45' => '14:45', '15:00' => '15:00', '15:15' => '15:15', '15:30' => '15:30', '15:45' => '15:45',
        '16:00' => '16:00', '16:15' => '16:15', '16:30' => '16:30', '16:45' => '16:45', '17:00' => '17:00', '17:15' => '17:15', '17:30' => '17:30', '17:45' => '17:45',
        '18:00' => '18:00', '18:15' => '18:15', '18:30' => '18:30', '18:45' => '18:45', '19:00' => '19:00', '19:15' => '19:15', '19:30' => '19:30', '19:45' => '19:45',
        '20:00' => '20:00', '20:15' => '20:15', '20:30' => '20:30', '20:45' => '20:45', '21:00' => '21:00', '21:15' => '21:15', '21:30' => '21:30', '21:45' => '21:45',
        '22:00' => '22:00', '22:15' => '22:15', '22:30' => '22:30', '22:45' => '22:45', '23:00' => '23:00', '23:15' => '23:15', '23:30' => '23:30', '23:45' => '23:45'
    ],

    'maps' => [
        'Google Normal' => 1,
        'OpenStreetMap' => 2,
        'Google Hybrid' => 3,
        'Google Satellite' => 4,
        'Google Terrain' => 5,
        'Yandex' => 6,
        'Bing Normal' => 7,
        'Bing Satellite' => 8,
        'Bing Hybrid' => 9,
        'Here Normal' => 10,
        'Here Sattelite' => 11,
        'Here Hybrid' => 12,
        'MapBox Normal' => 14,
        'MapBox Satellite' => 15,
        'MapBox Hybrid' => 16,
        'MapTiler Basic' => 17,
        'MapTiler Streets' => 18,
        'MapTiler Satellite' => 19,

        'OpenMapTiles OSM' => 21,

        'OpenRailway Infrastructure' => 22,
        'OpenRailway Max Speeds' => 23,
        'OpenRailway Signaling' => 24,
        'OpenRailway Electrification' => 25,

        'TomTom Basic' => 26,
        'TomTom Satellite' => 27,

        'Google Normal B' => 51,
        'Google Hybrid B' => 53,
        'Google Satellite B' => 54,
        'Google Terrain B' => 55,

        'Carto' => 70,
        'LocationIQ Streets' => 71,
    ],
    'frontend_login' => '',
    'frontend_subscriptions' => '',
    'frontend_change_password' => '',
    'frontend_curl' => '',
    'frontend_curl_password' => env('FRONTEND_PASSWORD', ''),

    'password' => [
        'min_length' => 8,
        'length' => 12,
        'includes' => [
            'lowercase',
            'uppercase',
            'numbers',
            'specials'
        ],
    ],

    'plans' => [],
    'min_database_clear_days' => 30,
    'max_history_period_days' => env('MAX_HISTORY_PERIOD_DAYS', 31),
    'demos' => [],
    'additional_protocols' => [
        'gpsdata' => 'gpsdata',
        'ios' => 'ios',
        'android' => 'android'
    ],
    'protocols' => [
        'gt02' => [
            'mergeable' => true
        ],
        'gt06' => [
            'mergeable' => true
        ],
        'gt062' => [
            'mergeable' => true
        ],
        'gps103' => [
            'mergeable' => true
        ],
        'h02' => [
            'mergeable' => true
        ],
        'eelink' => [
            'mergeable' => true
        ],
        'xirgo' => [
            'mergeable' => true
        ],
        'tk103' => [
            'mergeable' => true
        ],
        'gl200' => [
            'mergeable' => true,
            'expects' => ['power']
        ],
        'wialon' => [
            'mergeable' => true
        ],
        'aquila' => [
            'mergeable' => true
        ],
        'dualcam' => [
            'overwrite' => 'teltonika'
        ]
    ],
    'sensors' => [],
    'units_of_distance' => [],
    'units_of_capacity' => [],
    'units_of_altitude' => [],
    'date_formats' => [
        'Y-m-d' => 'yyyy-mm-dd',
        'm-d-Y' => 'mm-dd-yyyy',
        'd-m-Y' => 'dd-mm-yyyy'
    ],
    'time_formats' => [
        'H:i:s' => '24 hour clock',
        'h:i:s A' => 'AM/PM',
    ],
    'duration_formats' => [
        'standart' => 'h min s',
        'hh:mm:ss' => 'hh:mm:ss',
        'dd:hh:mm:ss' => 'dd:hh:mm:ss',
    ],
    'object_online_timeouts' => [],
    'zoom_levels' => [
        '19' => '19', '18' => '18', '17' => '17', '16' => '16', '15' => '15', '14' => '14', '13' => '13', '12' => '12', '11' => '11', '10' => '10', '9' => '9', '8' => '8', '7' => '7', '6' => '6', '5' => '5', '4' => '4', '3' => '3', '2' => '2', '1' => '1', '0' => '0',
    ],

    'permissions' => [
        'devices' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'beacons' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'alerts' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'geofences' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'routes' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'poi' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 1,
        ],
        'reports' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'drivers' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'custom_events' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'user_gprs_templates' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'user_sms_templates' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'sms_gateway' => [
            'view' => 1,
            'edit' => 0,
            'remove' => 0,
        ],
        'send_command' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'history' => [
            'view' => 1,
            'edit' => 0,
            'remove' => 1,
        ],
        'maintenance' => [
            'view' => 1,
            'edit' => 0,
            'remove' => 0,
        ],
        'camera' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'device_camera' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'tasks' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'chat' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 0,
        ],
        'media_categories' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'forwards' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'device.imei' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.sim_number' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.forward' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.protocol' => [
            'view' => 1,
            'edit' => 0,
            'remove' => 0,
        ],
        'device.expiration_date' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.installation_date' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.sim_activation_date' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.sim_expiration_date' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.msisdn' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.custom_fields' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.device_type_id' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'device.authentication' => [
            'view' => 1,
            'edit' => 1,
            'remove' => 0,
        ],
        'sharing' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'checklist_template' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'checklist' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'checklist_activity' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 0,
        ],
        'checklist_qr_code' => [
            'view'  => 1,
            'edit'  => 0,
            'remove'  => 0,
        ],
        'checklist_qr_pre_start_only' => [
            'view'  => 1,
            'edit'  => 0,
            'remove'  => 0,
        ],
        'checklist_optional_image' => [
            'view'  => 1,
            'edit'  => 0,
            'remove'  => 0,
        ],
        'device_configuration' => [
            'view'  => 1,
            'edit'  => 0,
            'remove'  => 0,
        ],
        'device_route_types' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'device_expenses' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'call_actions' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'widget_template_webhook' => [
            'view'  => 1,
            'edit'  => 0,
            'remove'  => 0,
        ],
        'custom_device_add' => [
            'view'  => 1,
            'edit'  => 0,
            'remove'  => 0,
        ],
        'external_url' => [
            'view'  => 1,
            'edit'  => 0,
            'remove'  => 0,
        ],
        'users' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 1,
        ],
        'user.login_token' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 0,
        ],
        'user.client_id' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 0,
        ],
        'user.login_periods' => [
            'view'  => 1,
            'edit'  => 1,
            'remove'  => 0,
        ],
    ],

    'permissions_modes' => [
        'view' => 1,
        'edit' => 1,
        'remove' => 1
    ],

    'restricted_permissions' => [
        'user' => [
            'device.expiration_date' => [
                'view' => 1,
                'edit' => 0,
                'remove' => 0
            ],
            'users' => [
                'view' => 0,
                'edit' => 0,
                'remove' => 0
            ],
        ]
    ],

    'numeric_sensors' => [
        'battery',
        'temperature',
        'temperature_calibration',
        'tachometer',
        'fuel_consumption',
        'fuel_tank_calibration',
        'fuel_tank',
        'satellites',
        'odometer',
        'gsm',
        'numerical',
        'load',
        'load_calibration',
        'speed_ecm',
    ],
    'listview_fields' => [
        'name' => [
            'field' => 'name',
            'class' => 'device'
        ],
        'imei' => [
            'field' => 'imei',
            'class' => 'device'
        ],
        'status' => [
            'field' => 'status',
            'class' => 'device'
        ],
        'speed' => [
            'field' => 'speed',
            'class' => 'device'
        ],
        'time' => [
            'field' => 'time',
            'class' => 'device'
        ],
        'protocol' => [
            'field' => 'protocol',
            'class' => 'device'
        ],
        'position' => [
            'field' => 'position',
            'class' => 'device'
        ],
        'address' => [
            'field' => 'address',
            'class' => 'device'
        ],
        'sim_number' => [
            'field' => 'sim_number',
            'class' => 'device'
        ],
        'device_model' => [
            'field' => 'device_model',
            'class' => 'device'
        ],
        'plate_number' => [
            'field' => 'plate_number',
            'class' => 'device'
        ],
        'vin' => [
            'field' => 'vin',
            'class' => 'device'
        ],
        'registration_number' => [
            'field' => 'registration_number',
            'class' => 'device'
        ],
        'object_owner' => [
            'field' => 'object_owner',
            'class' => 'device'
        ],
        'additional_notes' => [
            'field' => 'additional_notes',
            'class' => 'device'
        ],
        'group' => [
            'field' => 'group',
            'class' => 'device'
        ],
        'fuel' => [
            'field' => 'fuel',
            'class' => 'device'
        ],
        'stop_duration' => [
            'field' => 'stop_duration',
            'class' => 'device'
        ],
        'idle_duration' => [
            'field' => 'idle_duration',
            'class' => 'device'
        ],
        'ignition_duration' => [
            'field' => 'ignition_duration',
            'class' => 'device'
        ],
        'last_event_title' => [
            'field' => 'last_event_title',
            'class' => 'device'
        ],
        'last_event_type' => [
            'field' => 'last_event_type',
            'class' => 'device'
        ],
        'last_event_time' => [
            'field' => 'last_event_time',
            'class' => 'device'
        ],
        'sim_activation_date' => [
            'field' => 'sim_activation_date',
            'class' => 'device'
        ],
        'sim_expiration_date' => [
            'field' => 'sim_expiration_date',
            'class' => 'device'
        ],
        'installation_date' => [
            'field' => 'installation_date',
            'class' => 'device'
        ],
        'expiration_date' => [
            'field' => 'expiration_date',
            'class' => 'device'
        ],
    ],
    'listview' => [
        'groupby' => 'protocol',
        'columns' => [
            'name' => [
                'field' => 'name',
                'class' => 'device'
            ],
            'status' => [
                'field' => 'status',
                'class' => 'device'
            ],
            'time' => [
                'field' => 'time',
                'class' => 'device'
            ],
            'position' => [
                'field' => 'position',
                'class' => 'device'
            ]
        ]
    ],

    'plugins' => [
        'show_object_info_after' => [
            'status' => 0,
        ],
        'object_listview' => [
            'status' => 0,
        ],
        'business_private_drive' => [
            'status' => 0,
            'options' => [
                'business_color' => [
                    'value' => 'blue'
                ],
                'private_color' => [
                    'value' => 'red'
                ]
            ]
        ],
        'route_color' => [
            'status' => 0,
            'options' => [
                'value' => 'orange',
                'value_2' => 'red',
                'value_3' => 'black',
            ]
        ],
        'birla_report' => [
            'status' => 0,
        ],
        'object_history_report' => [
            'status' => 0,
        ],
        'automon_report' => [
            'status' => 0,
        ],
        'report_drives_stops_simlified' => [
            'status' => 0
        ],
        'report_stops' => [
            'status' => 0
        ],
        'report_travelsheet_custom' => [
            'status' => 0
        ],
        'report_cart_cleaning_daily' => [
            'status' => 0
        ],
        'additional_installation_fields' => [
            'status' => 0
        ],
        'annual_sim_expiration' => [
            'status' => 0,
            'options' => [
                'days' => 365
            ],
        ],
        'renew_sim_expiration' => [
            'status' => 0,
        ],
        'display_sim_expired' => [
            'status' => 0,
        ],
        'beacons' => [
            'status' => 0,
            'options' => [
                'detection_speed' => 0,
                'log' => [
                    'current' => true,
                    'history' => false,
                    'detach_on_no_position_data' => false,
                ],
            ]
        ],
        'send_sim_expiration_notification' => [
            'status' => 0,
        ],
        'overspeed_custom_report' => [
            'status' => 0,
        ],
        'offline_objects_report' => [
            'status' => 0
        ],
        'routes_report' => [
            'status' => 0
        ],
        'speed_report' => [
            'status' => 0
        ],
        'device_move_animation' => [
            'status' => 0
        ],
        'device_widget_total_distance' => [
            'status' => 0
        ],
        'device_widget_full_address' => [
            'status' => 0
        ],
        'alert_sharing' => [
            'status' => 0,
            'options' => [
                'duration' => [
                    'active' => false,
                    'value' => null,
                ],
                'delete_after_expiration' => [
                    'status' => false,
                ],
            ],
        ],
        'locking_widget' => [
            'status' => 0,
            'options' => [
                'parameter' => 'status',
                'value_on' => 'true',
                'value_off' => 'false',
            ],
        ],
        'speed_compare_gps_ecm_report' => [
            'status' => 0,
        ],
        'call_actions' => [
            'status' => 0,
        ],
        'create_only_expired_objects' => [
            'status' => 0,
            'options' => [
                'offset_type' => '',
                'offset' => '',
            ],
        ],
        'recent_events' => [
            'status' => 0,
        ],
        'sim_blocking' => [
            'status' => 0,
            'options' => [
                'provider' => '',
                'username' => '',
                'token' => '',
                'account_sid' => '',
            ]
        ],
        'geofence_size' => [
            'status' => 0,
        ],
        'geofence_over_address' => [
            'status' => 0,
        ],
        'history_section_address' => [
            'status' => 0,
        ],
        'event_section_address' => [
            'status' => 0,
        ],
        'event_section_alert' => [
            'status' => 0,
        ],
        'moving_geofence' => [
            'status' => 0,
        ],
        'device_driver_reset_engine' => [
            'status' => 0,
        ],
        'user_api_tab' => [
            'status' => 0,
        ],
        'send_command_speed_limit' => [
            'status' => 0,
            'options' => [
                'speed_limit' => 100,
                'commands' => [],
                'messages' => '',
            ]
        ],
        'geofences_speed_limit' => [
            'status' => 0,
        ],
    ],

    'process' => [
        'insert_timeout' => env('PROC_INSERT_TIMEOUT', 60),
        'insert_limit' => env('PROC_INSERT_LIMIT', 30),
        'reportdaily_timeout' => env('PROC_REPORT_TIMEOUT', 180),
        'reportdaily_limit' => env('PROC_REPORT_LIMIT', 2),
    ],

    'template_colors' => [
        'light-blue'        => 'Light Blue',
        'light-green'       => 'Light Green',
        'light-red'         => 'Light Red',
        'light-orange'      => 'Light Orange',
        'light-pink'        => 'Light Pink',
        'light-win10-blue'  => 'Light Win10 Blue',
        'light-indigo'      => 'Light Indigo',
        'light-black'       => 'Light Black',
        'dark-blue'         => 'Dark Blue',
        'dark-green'        => 'Dark Green',
        'dark-red'          => 'Dark Red',
        'dark-orange'       => 'Dark Orange',
        'dark-pink'         => 'Dark Pink',
        'dark-win10-blue'   => 'Dark Win10 Blue',
        'dark-indigo'       => 'Dark Indigo',
    ],

    'widgets' => [
        'default' => true,
        'status' => true,
        'list' => [
            'device', 'sensors', 'services', 'camera'
        ]
    ],

    'db_clear' => [
        'status' => true,
        'days'   => 90,
        'from'   => 'server_time'
    ],

    'limits' => [
        'alert_phones'          => env('LIMIT_ALERT_PHONES', 5),
        'alert_emails'          => env('LIMIT_ALERT_EMAILS', 5),
        'alert_webhooks'        => env('LIMIT_ALERT_WEBHOOKS', 2),
        'geofence_groups'       => env('LIMIT_GEOFENCE_GROUPS', 50),
        'report_emails'         => env('LIMIT_REPORT_EMAILS', 5),
        'command_devices'       => env('LIMIT_COMMAND_DEVICES', 10),
        'command_gprs_devices'  => env('LIMIT_COMMAND_GPRS_DEVICES', 0),
        'forward_ips'           => env('LIMIT_FORWARD_IPS', 5),
    ],

    'languages' => [
        'en' => [
            'key'    => 'en',
            'iso'    => 'en',
            'iso3'   => 'eng',
            'title'  => 'English(USA)',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'en.png',
            'locale' => 'en_US'
        ],
        'au' => [
            'key'    => 'au',
            'iso'    => 'en',
            'iso3'   => 'eng',
            'title'  => 'Australian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'au.png',
            'locale' => 'en_AU'
        ],
        'az' => [
            'key'    => 'az',
            'iso'    => 'az',
            'iso3'   => 'aze',
            'title'  => 'Azerbaijan',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'az.png',
            'locale' => 'az_AZ'
        ],
        'ar' => [
            'key'    => 'ar',
            'iso'    => 'ar',
            'iso3'   => 'ara',
            'title'  => 'Arabic',
            'active' => true,
            'dir'    => 'rtl',
            'flag'   => 'ar.png',
            'locale' => 'ar_AE'
        ],
        'sk' => [
            'key'    => 'sk',
            'iso'    => 'sk',
            'iso3'   => 'slo',
            'title'  => 'Slovakian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'sk.png',
            'locale' => 'sk'
        ],
        'th' => [
            'key'    => 'th',
            'iso'    => 'th',
            'iso3'   => 'tha',
            'title'  => 'Thai',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'th.png',
            'locale' => 'th'
        ],
        'nl' => [
            'key'    => 'nl',
            'iso'    => 'nl',
            'iso3'   => 'dut',
            'title'  => 'Dutch',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'nl.png',
            'locale' => 'nl_NL'
        ],
        'de' => [
            'key'    => 'de',
            'iso'    => 'de',
            'iso3'   => 'ger',
            'title'  => 'German',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'de.png',
            'locale' => 'de_DE'
        ],
        'gr' => [
            'key'    => 'gr',
            'iso'    => 'el',
            'iso3'   => 'gre',
            'title'  => 'Greek',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'gr.png',
            'locale' => 'el'
        ],
        'pl' => [
            'key'    => 'pl',
            'iso'    => 'pl',
            'iso3'   => 'pol',
            'title'  => 'Polish',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'pl.png',
            'locale' => 'pl'
        ],
        'uk' => [
            'key'    => 'uk',
            'iso'    => 'en',
            'iso3'   => 'eng',
            'title'  => 'English(UK)',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'uk.png',
            'locale' => 'en_GB'
        ],
        'fr' => [
            'key'    => 'fr',
            'iso'    => 'fr',
            'iso3'   => 'fre',
            'title'  => 'French',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'fr.png',
            'locale' => 'fr_FR'
        ],
        'br' => [
            'key'    => 'br',
            'iso'    => 'pt',
            'iso3'   => 'por',
            'title'  => 'Brazilian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'br.png',
            'locale' => 'pt_BR'
        ],
        'pt' => [
            'key'    => 'pt',
            'iso'    => 'pt',
            'iso3'   => 'por',
            'title'  => 'Portuguese',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'pt.png',
            'locale' => 'pt_PT'
        ],
        'es' => [
            'key'    => 'es',
            'iso'    => 'es',
            'iso3'   => 'spa',
            'title'  => 'Spanish',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'es.png',
            'locale' => 'es_ES'
        ],
        'it' => [
            'key'    => 'it',
            'iso'    => 'it',
            'iso3'   => 'ita',
            'title'  => 'Italian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'it.png',
            'locale' => 'it_IT'
        ],
        'ch' => [
            'key'    => 'ch',
            'iso'    => 'es',
            'iso3'   => 'spa',
            'title'  => 'Chile',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'ch.png',
            'locale' => 'es_CL'
        ],
        'sr' => [
            'key'    => 'sr',
            'iso'    => 'sr',
            'iso3'   => 'srp',
            'title'  => 'Serbian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'sr.png',
            'locale' => 'sr_SP'
        ],
        'fi' => [
            'key'    => 'fi',
            'iso'    => 'fi',
            'iso3'   => 'fin',
            'title'  => 'Finnish',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'fi.png',
            'locale' => 'fi'
        ],
        'dk' => [
            'key'    => 'dk',
            'iso'    => 'dk',
            'iso3'   => 'dan',
            'title'  => 'Danish',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'dk.png',
            'locale' => 'da'
        ],
        'ph' => [
            'key'    => 'ph',
            'iso'    => 'en',
            'iso3'   => 'eng',
            'title'  => 'Philippines',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'ph.png',
            'locale' => 'en_PH'
        ],
        'sv' => [
            'key'    => 'sv',
            'iso'    => 'sv',
            'iso3'   => 'swe',
            'title'  => 'Swedish',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'sv.png',
            'locale' => 'sv_SE'
        ],
        'ro' => [
            'key'    => 'ro',
            'iso'    => 'ro',
            'iso3'   => 'rum',
            'title'  => 'Romanian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'ro.png',
            'locale' => 'ro'
        ],
        'bg' => [
            'key'    => 'bg',
            'iso'    => 'bg',
            'iso3'   => 'bul',
            'title'  => 'Bulgarian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'bg.png',
            'locale' => 'bg'
        ],
        'hr' => [
            'key'    => 'hr',
            'iso'    => 'hr',
            'iso3'   => 'hrv',
            'title'  => 'Croatian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'hr.png',
            'locale' => 'hr'
        ],
        'cw' => [
            'key'    => 'cw',
            'iso'    => 'pt',
            'iso3'   => 'por',
            'title'  => 'Papiamento',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'cw.png',
            'locale' => 'pt_PT'
        ],
        'id' => [
            'key'    => 'id',
            'iso'    => 'id',
            'iso3'   => 'ind',
            'title'  => 'Indonesian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'id.png',
            'locale' => 'id'
        ],
        'ru' => [
            'key'    => 'ru',
            'iso'    => 'ru',
            'iso3'   => 'rus',
            'title'  => 'Russian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'ru.png',
            'locale' => 'ru_RU'
        ],
        'mk' => [
            'key'    => 'mk',
            'iso'    => 'mk',
            'iso3'   => 'mac',
            'title'  => 'Macedonian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'mk.png',
            'locale' => 'mk'
        ],
        'ir' => [
            'key'    => 'ir',
            'iso'    => 'fa',
            'iso3'   => 'per',
            'title'  => 'Persian',
            'active' => true,
            'dir'    => 'rtl',
            'flag'   => 'ir.png',
            'locale' => 'fa'
        ],
        'cn' => [
            'key'    => 'cn',
            'iso'    => 'zh',
            'iso3'   => 'chi',
            'title'  => 'Chinese',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'cn.png',
            'locale' => 'zh_CN'
        ],
        'nz' => [
            'key'    => 'nz',
            'iso'    => 'en',
            'iso3'   => 'eng',
            'title'  => 'New Zealand',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'nz.png',
            'locale' => 'en_NZ'
        ],
        'cz' => [
            'key'    => 'cz',
            'iso'    => 'cs',
            'iso3'   => 'cze',
            'title'  => 'Czech',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'cz.png',
            'locale' => 'cs'
        ],
        'he' => [
            'key'    => 'he',
            'iso'    => 'he',
            'iso3'   => 'heb',
            'title'  => 'Hebrew',
            'active' => true,
            'dir'    => 'rtl',
            'flag'   => 'il.png',
            'locale' => 'he'
        ],
        'hu' => [
            'key'    => 'hu',
            'iso'    => 'hu',
            'iso3'   => 'hun',
            'title'  => 'Hungarian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'hu.png',
            'locale' => 'hu'
        ],
        'ka' => [
            'key'    => 'ka',
            'iso'    => 'ka',
            'iso3'   => 'geo',
            'title'  => 'Georgian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'ka.png',
            'locale' => 'ka'
        ],
        'no' => [
            'key'    => 'no',
            'iso'    => 'no',
            'iso3'   => 'nor',
            'title'  => 'Norwegian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'no.png',
            'locale' => 'no_NO'
        ],
        'my' => [
            'key'    => 'my',
            'iso'    => 'my',
            'iso3'   => 'bur',
            'title'  => 'Burmese',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'my.png',
            'locale' => 'my'
        ],
        'ca' => [
            'key'    => 'ca',
            'iso'    => 'ca',
            'iso3'   => 'cat',
            'title'  => 'Catalan',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'catalonia.png',
            'locale' => 'ca'
        ],
        'tr' => [
            'key'    => 'tr',
            'iso'    => 'tr',
            'iso3'   => 'tur',
            'title'  => 'Turkish',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'tr.png',
            'locale' => 'tr'
        ],
        'ku' => [
            'key'    => 'ku',
            'iso'    => 'ku',
            'iso3'   => 'kur',
            'title'  => 'Kurdish',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'ku.png',
            'locale' => 'ku'
        ],
        'ja' => [
            'key'    => 'ja',
            'iso'    => 'ja',
            'iso3'   => 'jpn',
            'title'  => 'Japanese',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'jp.png',
            'locale' => 'ja'
        ],
        'ms' => [
            'key'    => 'ms',
            'iso'    => 'ms',
            'iso3'   => 'may',
            'title'  => 'Malay',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'malaysia.png',
            'locale' => 'ms'
        ],
        'si' => [
            'key'    => 'si',
            'iso'    => 'si',
            'iso3'   => 'sin',
            'title'  => 'Sinhala',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'sin.png',
            'locale' => 'si'
        ],
        'lo' => [
            'key'    => 'lo',
            'iso'    => 'lo',
            'iso3'   => 'lao',
            'title'  => 'Lao',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'la.png',
            'locale' => 'lo'
        ],
        'mn' => [
            'key'    => 'mn',
            'iso'    => 'mn',
            'iso3'   => 'mon',
            'title'  => 'Mongolian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'mn.png',
            'locale' => 'mn'
        ],
        'ta' => [
            'key'    => 'ta',
            'iso'    => 'ta',
            'iso3'   => 'tam',
            'title'  => 'Tamil',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'sin.png',
            'locale' => 'ta_IN'
        ],
        'hi' => [
            'key'    => 'hi',
            'iso'    => 'hi',
            'iso3'   => 'hin',
            'title'  => 'Hindi',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'in.png',
            'locale' => 'hi_IN'
        ],
        'ne' => [
            'key'    => 'ne',
            'iso'    => 'ne',
            'iso3'   => 'nep',
            'title'  => 'Nepali',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'np.png',
            'locale' => 'ne_NP'
        ],
        'sl' => [
            'key'    => 'sl',
            'iso'    => 'sl',
            'iso3'   => 'slv',
            'title'  => 'Slovene',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'si.png',
            'locale' => 'sl_SI'
        ],
        'lt' => [
            'key'    => 'lt',
            'iso'    => 'lt',
            'iso3'   => 'lit',
            'title'  => 'Lithuanian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'lt.png',
            'locale' => 'lt_LT'
        ],
        'lv' => [
            'key'    => 'lv',
            'iso'    => 'lv',
            'iso3'   => 'lav',
            'title'  => 'Latvian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'lv.png',
            'locale' => 'lv_LV'
        ],
        'al' => [
            'key'    => 'al',
            'iso'    => 'sq',
            'iso3'   => 'sqi',
            'title'  => 'Albanian',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'al.png',
            'locale' => 'sq_AL'
        ],
        'bn' => [
            'key'    => 'bn',
            'iso'    => 'bn',
            'iso3'   => 'ben',
            'title'  => 'Bengali',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'bd.png',
            'locale' => 'bn_BN'
        ],
        'ps' => [
            'key'    => 'ps',
            'iso'    => 'ps',
            'iso3'   => 'pus',
            'title'  => 'Pashto',
            'active' => true,
            'dir'    => 'ltr',
            'flag'   => 'af.png',
            'locale' => 'ps_PS'
        ],
    ],

    'sms_gateway' => [
        'enabled'               => false,
        'use_as_system_gateway' => false,
        'request_method'        => '',
        'sms_gateway_url'       => '',
        'custom_headers'        => '',
        'authentication'        => '0',
        'username'              => '',
        'password'              => '',
        'encoding'              => '',
        'auth_id'               => '',
        'auth_token'            => '',
        'senders_phone'         => '',
        'user_id'               => null
    ],

    'external_url' => [
        'enabled' => false,
        'external_url' => '',
    ],

    'position_notifications' => [
        'send_to' => env('POSITION_RESULT_NOTIFICATION', 'user'),
        'related_user_oldest_record_ago' => 5 * 60,
    ],
    'dualcam' => [
        'enabled' => env('DUALCAM', true),
    ],
    'login_periods' => [
        'enabled' => env('LOGIN_PERIODS', false),
    ],

    'object_delete_auth_confirm' => env('object_delete_auth_confirm', 0),
    
    'object_delete_pass' => env('object_delete_pass'),

    'payments' => [
        'gateways' => [
            'paypal'              => 0,
            'stripe'              => 0,
            'braintree'           => 0,
            'paydunya'            => 0,
            'mobile_direct_debit' => 0,
            'twocheckout'         => 0,
            'paysera'             => 0,
        ],

        'paypal' => [
            'currency'      => '',
            'payment_name'  => '',
            'client_id'     => '',
            'secret'        => '',
            'mode'          => '',
        ],

        'stripe' => [
            'public_key'  => '',
            'secret_key'  => '',
            'currency'    => '',
            'webhook_key' => '',
        ],

        'braintree' => [
            'environment'           => 'sandbox',
            'merchantId'            => '',
            'publicKey'             => '',
            'privateKey'            => '',
            'merchant_account_id'   => null,
            '3d_secure'             => false,
            'plans' => [
                // server_billing_plan_id => braintree_plan_id
            ],

        ],

        'paydunya' => [
            'mode'          => '',
            'master_key'    => '',
            'public_key'    => '',
            'private_key'   => '',
            'token'         => '',
            'payment_name'  => ''
        ],

        'mobile_direct_debit' => [
            'url'         => '',
            'api_key'     => '',
            'merchant_id' => '',
            'product_id'  => ''
        ],

        'twocheckout' => [
            'front_url' => 'https://www.2checkout.com',
            'api_url' => 'https://api.2checkout.com/rest/6.0',
            'merchant_code' => '',
            'secret_key' => '',
            'demo_mode' => false,
        ],

        'paysera' => [
            'project_id'    => '',
            'project_psw'   => '',
            'verify_id'     => '',
            'currency'      => '',
            'environment'   => 'sandbox',
        ],

        'kevin' => [
            'client_id'         => '',
            'client_secret'     => '',
            'endpoint_secret'   => '',
            'currency'          => '',
            'language'          => 'en',
            'receiver_name'     => '',
            'receiver_iban'     => '',
        ],
    ],

    'backups' => [
        'type'         => 'custom',
        'period'       => 1,
        'hour'         => '00:00',
        'ftp_server'   => null,
        'ftp_username' => null,
        'ftp_password' => null,
        'ftp_port'     => null,
        'ftp_path'     => null,
    ],

    'exports' => [
        'formats' => [
            'csv' => 'CSV',
            'xls' => 'XLS'
        ]
    ],

    'dashboard' => [
        'enabled' => 0,
        'blocks'  => [
            'device_activity'      => [
                'enabled' => 1,
                'options' => [],
            ],
            'latest_events'        => [
                'enabled' => 1,
                'options' => [
                    'period' => 'day',
                ],
            ],
            'device_status_counts' => [
                'enabled' => 1,
                'options' => [],
            ],
            'latest_tasks'         => [
                'enabled' => 1,
                'options' => [
                    'period' => 'day',
                ],
            ],
            'device_distance'      => [
                'enabled' => 1,
                'options' => [
                    'devices' => []
                ],
            ],
            'device_overview'      => [
                'enabled' => 0,
                'options' => [],
            ]
        ],
    ],

    'weekdays'    => [
        'monday'    => 'front.monday',
        'tuesday'   => 'front.tuesday',
        'wednesday' => 'front.wednesday',
        'thursday'  => 'front.thursday',
        'friday'    => 'front.friday',
        'saturday'  => 'front.saturday',
        'sunday'    => 'front.sunday',
    ],

    'device_configuration' => [
        'delay' => env('DEVICE_CONFIGURATION_DELAY', 5),
    ],

    'currency' => [
        'symbol' => '$',
    ],

    'extra_required_fields' => [
        'device' => [/* 'field' => 'required_if' */],
    ],
];