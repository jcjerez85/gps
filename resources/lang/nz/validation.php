<?php

return array(

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'between'  => array(
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ),
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'email' => 'The :attribute must be a valid email address.',
    'exists' => 'The selected :attribute is invalid.',
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'max'  => array(
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ),
    'mimes' => 'The :attribute must be a file of type: :values.',
    'min'  => array(
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ),
    'not_in' => 'The selected :attribute is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values is present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size'  => array(
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ),
    'unique' => 'The :attribute has already been taken.',
    'url' => 'The :attribute format is invalid.',
    'array_max' => 'The :attribute max items :max.',
    'lesser_than' => 'The :attribute must be lesser than :other',
    'custom'  => array(
        'attribute-name'  => array(
            'rule-name' => 'custom-message',
        ),
        'frontpage_logo'  => array(
            'dimensions' => 'Frontpage logo max height is 60px.',
        ),
        'favicon'  => array(
            'dimensions' => 'Favicon must be 16px by 16px.',
        ),
    ),
    'attributes'  => array(
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Password confirmation',
        'remember_me' => 'Remember me',
        'name' => 'Name',
        'imei' => 'IMEI',
        'imei_device' => 'IMEI or Device identifier',
        'fuel_measurement_type' => 'Fuel measurement',
        'fuel_cost' => 'Fuel cost',
        'icon_id' => 'Device icon',
        'active' => 'Active',
        'polygon_color' => 'Background color',
        'devices' => 'Devices',
        'geofences' => 'Geofences',
        'overspeed' => 'Overspeed',
        'fuel_consumption' => 'Fuel consumption',
        'description' => 'Description',
        'map_icon_id' => 'Marker icon',
        'coordinates' => 'Map point',
        'date_from' => 'Date from',
        'date_to' => 'Date to',
        'code' => 'Code',
        'title' => 'Title',
        'note' => 'Content',
        'path' => 'File',
        'period_name' => 'Period name',
        'days' => 'Days',
        'devices_limit' => 'Devices limit',
        'trial' => 'Trial',
        'price' => 'Price',
        'message' => 'Message',
        'tag' => 'Parameter',
        'timezone_id' => 'Timezone',
        'unit_of_distance' => 'Unit of distance',
        'unit_of_capacity' => 'Unit of capacity',
        'user' => 'User',
        'group_id' => 'Group',
        'permission_to_add_devices' => 'Add devices one two',
        'unit_of_altitude' => 'Unit of altitude',
        'sms_gateway_url' => 'SMS gateway URL',
        'mobile_phone' => 'Mobile phone',
        'permission_to_use_sms_gateway' => 'SMS gateway',
        'loged_at' => 'Last login',
        'manager_id' => 'Manager',
        'sim_number' => 'SIM number',
        'device_model' => 'Device model',
        'rfid' => 'RFID',
        'phone' => 'Phone',
        'device_id' => 'Device',
        'tag_value' => 'Parameter value',
        'device_port' => 'Device port',
        'event' => 'Event',
        'port' => 'Port',
        'device_protocol' => 'Device protocol',
        'protocol' => 'Protocol',
        'sensor_name' => 'Sensor name',
        'sensor_type' => 'Sensor type',
        'sensor_template' => 'Sensor template',
        'tag_name' => 'Parameter name',
        'min_value' => 'Min. value',
        'max_value' => 'Max. value',
        'on_value' => 'ON value',
        'off_value' => 'OFF value',
        'shown_value_by' => 'Show value by',
        'full_tank_value' => 'Parameter value',
        'formula' => 'Formula',
        'parameters' => 'Parameters',
        'full_tank' => 'Full tank in liters/gallons',
        'fuel_tank_name' => 'Fuel tank name',
        'odometer_value' => 'Value',
        'odometer_value_by' => 'Odometer',
        'unit_of_measurement' => 'Unit of measurement',
        'plate_number' => 'Plate number',
        'vin' => 'VIN',
        'registration_number' => 'Registration/Asset number',
        'object_owner' => 'Object owner/Manager',
        'additional_notes' => 'Additional notes',
        'expiration_date' => 'Expiration date',
        'days_to_remind' => 'Days to remind before expiration',
        'type' => 'Type',
        'format' => 'Format',
        'show_addresses' => 'Show addresses',
        'stops' => 'Stops',
        'speed_limit' => 'Speed limit',
        'zones_instead' => 'Zones instead of addresses',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'send_to_email' => 'Send to email',
        'filter' => 'Filter',
        'status' => 'Status',
        'date' => 'Date',
        'geofence_name' => 'Geofence name',
        'tail_color' => 'Tail color',
        'tail_length' => 'Tail length',
        'engine_hours' => 'Engine hours',
        'detect_engine' => 'Detect engine ON/OFF by',
        'min_moving_speed' => 'Min. moving speed in km/h',
        'min_fuel_fillings' => 'Min. fuel difference to detect fuel fillings',
        'min_fuel_thefts' => 'Min. fuel difference to detect fuel thefts',
        'expiration_by' => 'Expiration by',
        'interval' => 'Interval',
        'last_service' => 'Last service',
        'trigger_event_left' => 'Trigger event when left',
        'current_odometer' => 'Current odometer',
        'current_engine_hours' => 'Current engine hours',
        'renew_after_expiration' => 'Renew after expiration',
        'sms_template_id' => 'SMS template',
        'frequency' => 'Frequency',
        'unit' => 'Unit',
        'noreply_email' => 'No reply email address',
        'signature' => 'Signature',
        'use_smtp_server' => 'Use SMTP server',
        'smtp_server_host' => 'SMTP server host',
        'smtp_server_port' => 'SMTP server port',
        'smtp_security' => 'SMTP security',
        'smtp_username' => 'SMTP username',
        'smtp_password' => 'SMTP password',
        'from_name' => 'From name',
        'icons' => 'Icons',
        'server_name' => 'Server name',
        'available_maps' => 'Available maps',
        'default_language' => 'Default language',
        'default_timezone' => 'Default timezone',
        'default_unit_of_distance' => 'Default unit of distance',
        'default_unit_of_capacity' => 'Default unit of capacity',
        'default_unit_of_altitude' => 'Default unit of altitude',
        'default_date_format' => 'Default date format',
        'default_time_format' => 'Default time format',
        'default_map' => 'Default map',
        'default_object_online_timeout' => 'Default object online timeout',
        'logo' => 'Logo',
        'login_page_logo' => 'Login page logo',
        'frontpage_logo' => 'Frontpage logo',
        'favicon' => 'Favicon',
        'allow_users_registration' => 'Allow users registration',
        'default_maps' => 'Default maps',
        'subscription_expiration_after_days' => 'Subscription expiration after days',
        'gprs_template_id' => 'GPRS template',
        'calibrations' => 'Calibrations',
        'ftp_server' => 'FTP server',
        'ftp_port' => 'FTP port',
        'ftp_username' => 'FTP username',
        'ftp_password' => 'FTP password',
        'ftp_path' => 'FTP path',
        'period' => 'Period',
        'hour' => 'Hour',
        'color' => 'Color',
        'polyline' => 'Route',
        'request_method' => 'Request method',
        'authentication' => 'Authentication',
        'username' => 'Username',
        'encoding' => 'Encoding',
        'time_adjustment' => 'Time adjustment',
        'parameter' => 'Parameter',
        'export_type' => 'Export type',
        'groups' => 'Groups',
        'file' => 'File',
        'extra' => 'Extra',
        'parameter_value' => 'Parameter value',
        'enable_plans' => 'Enable plans',
        'payment_type' => 'Payment type',
        'paypal_client_id' => 'Paypal client ID',
        'paypal_secret' => 'Paypal secret',
        'paypal_currency' => 'Paypal currency',
        'paypal_payment_name' => 'Paypal payment name',
        'objects' => 'Objects',
        'duration_value' => 'Duration',
        'permissions' => 'Permissions',
        'plan' => 'Plan',
        'default_billing_plan' => 'Default billing plan',
        'sensor_group_id' => 'Sensor group',
        'daylight_saving_time' => 'Daylight saving time',
        'phone_number' => 'Phone number',
        'action' => 'Action',
        'time' => 'Time',
        'order' => 'Order',
        'geocoder_api' => 'Geocoder API',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'API key',
        'api_url' => 'API url',
        'map_center_latitude' => 'Map center latitude',
        'map_center_longitude' => 'Map center longitude',
        'map_zoom_level' => 'Map zoom level',
        'dst_type' => 'Type',
        'provider' => 'Provider',
        'week_start_day' => 'Default calendar week start day',
        'ip' => 'IP',
        'gprs_templates_only' => 'Show GPRS Templates commands only',
        'select_all_objects' => 'Select all objects',
        'icon_type' => 'Icon type',
        'on_setflag_1' => 'Starting character',
        'on_setflag_2' => 'Amount of characters',
        'on_setflag_3' => 'Value of parameter',
        'domain' => 'Domain',
        'auth_id' => 'Auth ID',
        'auth_token' => 'Auth token',
        'senders_phone' => 'Sender\'s phone number',
        'database_clear_status' => 'Automatic history cleanup',
        'database_clear_days' => 'Days to keep',
        'ignition_detection' => 'Ignition detection by',
        'here_map_id' => 'HERE.com app ID',
        'here_map_code' => 'HERE.com app code',
        'login_page_panel_background_color' => 'Login page panel background color',
        'login_page_panel_transparency' => 'Login page panel transparency',
        'visible' => 'Visible',
        'template_color' => 'Template color',
        'background' => 'Background',
        'login_page_text_color' => 'Login page text color',
        'login_page_background_color' => 'Login page background color',
        'welcome_text' => 'Welcome text',
        'bottom_text' => 'Bottom text',
        'apple_store_link' => 'Apple store link',
        'google_play_link' => 'Google play link',
        'position' => 'Position',
        'stop_duration_longer_than' => 'Stop duration longer than',
        'mapbox_access_token' => 'MapBox access token',
        'flag' => 'Flag',
        'shift_start' => 'Shift start',
        'shift_finish' => 'Shift finish',
        'shift_start_tolerance' => 'Shift start tolerance',
        'shift_finish_tolerance' => 'Shift finish tolerance',
        'excessive_exit' => 'Excessive exit',
        'smtp_authentication' => 'SMTP authentication',
        'skip_calibration' => 'Exclude calculations out of the calibration range',
        'bing_maps_key' => 'Bing maps key',
        'stripe_public_key' => 'STRIPE public key',
        'stripe_secret_key' => 'STRIPE secret key',
        'stripe_currency' => 'STRIPE currency',
        'priority' => 'Priority',
        'pickup_address' => 'Pickup address',
        'delivery_address' => 'Delivery address',
        'schedule' => 'Schedule',
        'sound_notification' => 'Sound notification',
        'push_notification' => 'Push notification',
        'email_notification' => 'Email notification',
        'sms_notification' => 'SMS notification',
        'webhook_notification' => 'Webhook notification',
        'offline_duration_longer_than' => 'Offline duration longer than',
        'sms_gateway_headers' => 'SMS gateway headers',
        'forward' => 'Forward',
        'by_status' => 'By status',
        'icon_status_online' => 'Online status icon',
        'icon_status_offline' => 'Offline status icon',
        'icon_status_ack' => 'Ack status icon',
        'icon_status_engine' => 'Engine status icon',
        'server_description' => 'Server description',
        'bypass_invalid' => 'Allow invalid coordinates',
        'installation_date' => 'Installation date',
        'sim_activation_date' => 'SIM activation date',
        'sim_expiration_date' => 'SIM expiration date',
        'activation_date' => 'Activation date',
        'secret_key' => 'Secret key',
        'currency' => 'Currency',
        'client_id' => 'Client ID',
        'secret' => 'Secret',
        'payment_name' => 'Payment name',
        'merchant_id' => 'Merchant ID',
        'public_key' => 'Public key',
        'private_key' => 'Private key',
        'braintree_plan_ids' => 'Braintree plan IDs for server plans',
        'braintree_plan_explanation' => 'You must create reccurent billing plan in Braintree control panel, select the ID here corresponding billing plan on your server.',
        'braintree_merchant_explanation' => 'You must create merchant account in Braintree control panel and enter the ID here.',
        'braintree_currency_match' => 'Merchant account currency must match plan currency',
        'merchant_account_id' => 'Merchant account ID',
        'master_key' => 'Master key',
        'token' => 'Token',
        'paydunya_currency_warning' => 'The only available currency is FCFA. If you configure it please make sure your other payments match the same currency. Otherwise users will have a chance to buy the same plan with different prices.',
        'country' => 'Country',
        'merchant_account' => 'Merchant account',
        'origin_key' => 'Origin key',
        'test_config' => 'Test config',
        'environment' => 'Environment',
        'three_letter_iso' => 'Three-letter ISO currency code',
        'google_maps_key' => 'Google maps API key',
        'maptiler_key' => 'MapTiler key',
        'streetview_api' => 'Streetview API',
        'streetview_key' => 'Streetview API key',
        'openmaptiles_url' => 'OpenMapTiles Url',
        'unit_cost' => 'Unit cost',
        'supplier' => 'Supplier',
        'buyer' => 'Buyer',
        'expense_type' => 'Expense type',
        'device_cameras_days' => 'Days to keep device camera\'s images',
        'api_app_id' => 'App ID',
        'api_app_code' => 'App Code',
        'api_app_secret' => 'App Secret',
        'invoice_number' => 'Invoice number',
        'one_time_payment' => 'One time payment',
        'sharing_id' => 'Sharing',
        'idle_duration_longer_than' => 'Idle duration longer than',
        'delete_after_expiration' => 'Delete after expiration',
        'sharing_email' => 'Email notification with sharing link',
        'sharing_sms' => 'SMS notification with sharing link',
        'sms' => 'SMS',
        'template' => 'Template',
        'commands' => 'Commands',
        'brand' => 'Device manufacturer',
        'model' => 'Model',
        'apn_name' => 'APN name',
        'apn_username' => 'APN username',
        'apn_password' => 'APN password',
        'ignition_duration_longer_than' => 'Ignition duration longer than',
        'tasks' => 'Tasks',
        'id' => 'ID',
        'columns' => 'Columns',
        'called_at' => 'Call at',
        'alert_type' => 'Alert type',
        'response' => 'Response',
        'remarks' => 'Remarks',
        'client' => 'Client',
        'event_type' => 'Event type',
        'data_type' => 'Data type',
        'slug' => 'Slug',
        'required' => 'Required',
        'validation' => 'Validation',
        'text' => 'Text',
        'datetime' => 'Datetime',
        'boolean' => 'Boolean',
        'select' => 'Select',
        'multiselect' => 'Multiselect',
        'options' => 'Options',
        'option' => 'Option',
        'default' => 'Default',
        'msisdn' => 'MSISDN',
        'notes' => 'Notes',
        'skip_empty' => 'Skip empty value',
        'distance_limit' => 'Distance limit',
        'distance_tolerance' => 'Distance tolerance',
        'pois' => 'POIs',
        'device_type_id' => 'Device type',
        'custom_fields' => 'Custom fields',
        'device_name' => 'Device name',
        'auto_hide_notification' => 'Auto hide popup',
        'continuous_duration' => 'Continuous duration',
        'captcha_provider' => 'CAPTCHA provider',
        'google_recaptcha' => 'Google reCAPTCHA',
        'recaptcha_site_key' => 'reCAPTCHA site key',
        'recaptcha_secret_key' => 'reCAPTCHA secret key',
        'g-recaptcha-response' => 'reCAPTCHA',
        'here_api_key' => 'HERE.com API key',
        'time_duration' => 'Time duration',
        'offset' => 'Offset',
        'geofence_device' => 'Device',
        'webhook_key' => 'Webhook key',
        'skip_blank_results' => 'Skip blank results',
        'account_sid' => 'Account SID',
        'speed_limit_tolerance' => 'Speed limit tolerance',
        'started_at' => 'Start time',
        'ended_at' => 'End time',
        'region' => 'Region',
        'adapted' => 'Adapted',
        'silent_notification' => 'Ignore notifications if repeated in minutes',
        'tomtom_key' => 'TomTom key',
        'authorized' => 'Authorized',
        'email_verification' => 'Email verification',
        'project_id' => 'Project ID',
        'project_psw' => 'Project password',
        'verify_id' => 'Verify id',
        'app_tracker_login' => 'Tracker app login enabled',
        'merchant_code' => 'Merchant code',
        'count' => 'Count',
        'detect_distance' => 'Distance detection by',
        'detect_speed' => 'Speed detection by',
        'routes' => 'Routes',
        'battery_threshold' => 'Battery threshold',
        'state' => 'State',
        'duration' => 'Duration',
        'statuses' => 'Statuses',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'personal_code' => 'Personal code',
        'birth_date' => 'Birth date',
        'company_id' => 'Company',
        'registration_code' => 'Registration code',
        'vat_number' => 'VAT number',
        'address' => 'Address',
        'comment' => 'Comment',
        'duration_format' => 'Duration format',
        'default_duration_format' => 'Default duration format',
        'login_token' => 'Token',
        'monthly' => 'Monthly',
        'amount' => 'Amount',
        'bad_sms_gateway_url' => 'Bad SMS gateway url or configuration',
        'rates' => 'Rates',
        'fields' => 'Fields',
        'tenant_id' => 'Tenant ID',
        'client_secret' => 'Client secret',
        'default_login_methods' => 'Default login methods',
        'forwards' => 'Forwards',
        'detection_speed' => 'Detection speed',
        'detach_on_no_position_data' => 'Detach on no position data',
        'extra_expiration_time' => 'Extra expiration time',
        'fuel_detect_sec_after_stop' => 'Detect fuel change after stop',
        'login_periods' => 'Login periods',
    ),
    'same_protocol' => 'The devices must be of same protocol.',
    'contains' => 'The :attribute must contain :value.',
    'ip_port' => 'The :attribute does not match the format IP:PORT',
    'required_unless' => 'The :attribute field is required.',
    'translation_file' => 'Translation file don\'t exist',
    'placeholder' => 'Attribute ":placeholder" is missing',
    'image_valid' => 'The :attribute must be an image.',
    'missing_configuration_value' => 'Missing :attribute configuration value.',
    'sms_gateway_error' => 'Message can\'t be sent. Sms gateway error.',
    'cant_configure_device' => 'Couldn\'t configure device',
    'field_does_not_exist' => 'Field :attribute doesn\'t exist',
    'unsupported_rules' => 'Unsupported rules:',
    'unsupported_parameterized_rules' => 'Unsupported parameterized rules:',
    'cant_update_custom_field' => 'Can\'t update field ":field" because there is existing records using this custom field',
    'strong_password' => 'Strong password',
    'uppercase_character' => 'Uppercase character required',
    'lowercase_character' => 'Lowercase character required',
    'digit_character' => 'Digit character required',
    'wrong_captcha' => 'Wrong CAPTCHA',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'uploaded' => 'The :attribute failed to upload. It is possible that the server does not accept such a size.',
    'login_methods'  => array(
        'email' => 'Email',
        'azure' => 'Microsoft Azure',
    ),
    'host' => 'The :attribute is not valid host',
    'host_port' => 'The :attribute does not match the format HOST:PORT',
    'unique_table_msg' => 'The :attribute has already been taken in :table',
);