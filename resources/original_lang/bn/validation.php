<?php

return array(

    'accepted' => 'এই :attribute অবশ্যই গ্রহণ করতে হবে।',
    'active_url' => ':attribute কোনও বৈধ URL নয়।',
    'after' => ':attribute পরের একটি তারিখ হওয়া আবশ্যক :date',
    'alpha' => ':attribute কেবলমাত্র অক্ষর থাকতে পারে।',
    'alpha_dash' => ':attribute কেবলমাত্র অক্ষর, সংখ্যা এবং ড্যাশ থাকতে পারে।',
    'alpha_num' => ':attribute কেবল অক্ষর এবং সংখ্যা থাকতে পারে।',
    'array' => ':attribute অবশ্যই একটি অ্যারে হতে হবে।',
    'before' => ':attribute আগের একটি তারিখ হওয়া আবশ্যক :date',
    'between'  => array(
        'numeric' => ':attribute :min এবং :max মধ্যে হতে হবে।',
        'file' => ':attribute এর মধ্যে হওয়া উচিত :min এবং :max কিলোবাইট।',
        'string' => 'মান :attribute :min এবং :max অক্ষরের মধ্যে হওয়া আবশ্যক।',
        'array' => ':attribute :min এবং :max আইটেমের মধ্যে থাকা অবশ্যই:',
    ),
    'confirmed' => 'এই :attribute নিশ্চিতকরণ মেলে না।',
    'date' => ':attribute বৈধ তারিখ নয়।',
    'date_format' => ':attribute :format মেলে না।',
    'different' => ':attribute এবং :other অবশ্যই আলাদা হতে হবে।',
    'digits' => ':attribute হতে হবে :digits সংখ্যা।',
    'digits_between' => ':attribute :min এবং :max অঙ্কের মধ্যে হওয়া আবশ্যক।',
    'email' => ':attribute অবশ্যই একটি বৈধ ইমেল ঠিকানা হতে হবে।',
    'exists' => 'নির্বাচিত :attribute অবৈধ।',
    'image' => ':attribute অবশ্যই একটি চিত্র হতে হবে।',
    'in' => 'নির্বাচিত :attribute অবৈধ।',
    'integer' => ':attribute অবশ্যই পূর্ণসংখ্যা হয়।',
    'ip' => ':attribute অবশ্যই একটি বৈধ আইপি ঠিকানা হতে হবে।',
    'max'  => array(
        'numeric' => 'মান :attribute চেয়ে বেশি হতে পারে না :max ।',
        'file' => 'এর :attribute চেয়ে বেশি হতে পারে না :max কিলোবাইট।',
        'string' => 'মান :attribute চেয়ে বেশি হতে পারে না :max অক্ষর।',
        'array' => ':attribute :max আইটেমের বেশি না থাকতে পারে।',
    ),
    'mimes' => ':attribute অবশ্যই টাইপের একটি ফাইল হতে হবে :values ।',
    'min'  => array(
        'numeric' => ':attribute অবশ্যই কমপক্ষে :min হতে হবে।',
        'file' => ':attribute :min কিলোবাইট হতে হবে।',
        'string' => ':attribute অবশ্যই কমপক্ষে :min অক্ষর হতে হবে।',
        'array' => ':attribute কমপক্ষে :min আইটেম থাকতে হবে।',
    ),
    'not_in' => 'নির্বাচিত :attribute অবৈধ।',
    'numeric' => ':attribute একটি সংখ্যা হতে হবে।',
    'regex' => 'এই :attribute বিন্যাসটি অবৈধ।',
    'required' => 'এই :attribute ক্ষেত্র প্রয়োজন।',
    'required_if' => 'এই :attribute ক্ষেত্র প্রয়োজন।',
    'required_with' => ':values উপস্থিত থাকলে :attribute ক্ষেত্রটি প্রয়োজন।',
    'required_with_all' => ':values উপস্থিত থাকলে :attribute ক্ষেত্রটি প্রয়োজন।',
    'required_without' => ':values উপস্থিত না থাকলে :attribute ক্ষেত্রটি প্রয়োজন।',
    'required_without_all' => ':values মধ্যে কোনওটি উপস্থিত না থাকলে :attribute',
    'same' => 'এই :attribute এবং :other অবশ্যই মিলবে।',
    'size'  => array(
        'numeric' => 'এর :attribute অবশ্যই হতে হবে :size ।',
        'file' => ':attribute হতে হবে :size কিলোবাইট।',
        'string' => 'এর :attribute অবশ্যই হতে হবে :size অক্ষর।',
        'array' => 'এই :attribute অবশ্যই থাকতে হবে :size আইটেম।',
    ),
    'unique' => ':attribute ইতিমধ্যে নেওয়া হয়েছে।',
    'url' => 'এই :attribute বিন্যাসটি অবৈধ।',
    'array_max' => 'এই :attribute সর্বাধিক আইটেম :max ।',
    'lesser_than' => ':attribute চেয়ে ক্ষুদ্রতর হতে হবে :other',
    'custom'  => array(
        'attribute-name'  => array(
            'rule-name' => 'কাস্টম বার্তা',
        ),
        'frontpage_logo'  => array(
            'dimensions' => 'সম্মুখ পৃষ্ঠার লোগো সর্বাধিক উচ্চতা 60px।',
        ),
        'favicon'  => array(
            'dimensions' => 'ফ্যাভিকন 16px বাই 16px হতে হবে',
        ),
    ),
    'attributes'  => array(
        'email' => 'ইমেল',
        'password' => 'পাসওয়ার্ড',
        'password_confirmation' => 'পাসওয়ার্ড নিশ্চিতকরণ',
        'remember_me' => 'আমাকে মনে কর',
        'name' => 'নাম',
        'imei' => 'আইএমইআই',
        'imei_device' => 'আইএমইআই বা ডিভাইস সনাক্তকারী',
        'fuel_measurement_type' => 'জ্বালানী পরিমাপ',
        'fuel_cost' => 'জ্বালানী খরচ',
        'icon_id' => 'ডিভাইস আইকন',
        'active' => 'সক্রিয়',
        'polygon_color' => 'পেছনের রঙ',
        'devices' => 'ডিভাইসগুলি',
        'geofences' => 'জিওফেন্সস',
        'overspeed' => 'ওভারস্পিড',
        'fuel_consumption' => 'জ্বালানি খরচ',
        'description' => 'বর্ণনা',
        'map_icon_id' => 'চিহ্নিতকারী আইকন',
        'coordinates' => 'মানচিত্রের পয়েন্ট',
        'date_from' => 'তারিখ হতে',
        'date_to' => 'তারিখ',
        'code' => 'কোড',
        'title' => 'শিরোনাম',
        'note' => 'বিষয়বস্তু',
        'path' => 'ফাইল',
        'period_name' => 'পিরিয়ডের নাম',
        'days' => 'দিনগুলি',
        'devices_limit' => 'ডিভাইস সীমা',
        'trial' => 'বিচার',
        'price' => 'দাম',
        'message' => 'বার্তা',
        'tag' => 'প্যারামিটার',
        'timezone_id' => 'সময় অঞ্চল',
        'unit_of_distance' => 'দূরত্বের একক',
        'unit_of_capacity' => 'ক্ষমতা একক',
        'user' => 'ব্যবহারকারী',
        'group_id' => 'দল',
        'permission_to_add_devices' => 'এক দুটি ডিভাইস যুক্ত করুন',
        'unit_of_altitude' => 'উচ্চতার ইউনিট',
        'sms_gateway_url' => 'এসএমএস গেটওয়ে ইউআরএল',
        'mobile_phone' => 'মোবাইল ফোন',
        'permission_to_use_sms_gateway' => 'এসএমএস গেটওয়ে',
        'loged_at' => 'শেষ লগইন',
        'manager_id' => 'ম্যানেজার',
        'sim_number' => 'সিম নম্বর',
        'device_model' => 'ডিভাইস মডেল',
        'rfid' => 'আরএফআইডি',
        'phone' => 'ফোন',
        'device_id' => 'যন্ত্র',
        'tag_value' => 'প্যারামিটার মান',
        'device_port' => 'ডিভাইস বন্দর',
        'event' => 'ইভেন্ট',
        'port' => 'বন্দর',
        'device_protocol' => 'ডিভাইস প্রোটোকল',
        'protocol' => 'প্রোটোকল',
        'sensor_name' => 'সেন্সর নাম',
        'sensor_type' => 'সেন্সর টাইপ',
        'sensor_template' => 'সেন্সর টেম্পলেট',
        'tag_name' => 'প্যারামিটারের নাম',
        'min_value' => 'নূন্যতম। মান',
        'max_value' => 'সর্বাধিক মান',
        'on_value' => 'অন মান',
        'off_value' => 'বন্ধ মূল্য',
        'shown_value_by' => 'দ্বারা মান দেখান',
        'full_tank_value' => 'প্যারামিটার মান',
        'formula' => 'সূত্র',
        'parameters' => 'পরামিতি',
        'full_tank' => 'লিটার / গ্যালনগুলিতে পূর্ণ ট্যাঙ্ক',
        'fuel_tank_name' => 'জ্বালানী ট্যাঙ্কের নাম',
        'odometer_value' => 'মান',
        'odometer_value_by' => 'ওডোমিটার',
        'unit_of_measurement' => 'পরিমাপের একক',
        'plate_number' => 'প্লেট নম্বর',
        'vin' => 'ভিন',
        'registration_number' => 'নিবন্ধকরণ / সম্পদ নম্বর',
        'object_owner' => 'অবজেক্টের মালিক / ম্যানেজার',
        'additional_notes' => 'অতিরিক্ত নোট',
        'expiration_date' => 'মেয়াদ শেষ হওয়ার তারিখ',
        'days_to_remind' => 'মেয়াদ শেষ হওয়ার আগে মনে রাখার দিনগুলি',
        'type' => 'প্রকার',
        'format' => 'ফর্ম্যাট',
        'show_addresses' => 'ঠিকানাগুলি দেখান',
        'stops' => 'থামছে',
        'speed_limit' => 'গতিসীমা',
        'zones_instead' => 'ঠিকানার পরিবর্তে অঞ্চলগুলি',
        'daily' => 'প্রতিদিন',
        'weekly' => 'সাপ্তাহিক',
        'send_to_email' => 'ইমেল পাঠান',
        'filter' => 'ছাঁকনি',
        'status' => 'স্থিতি',
        'date' => 'তারিখ',
        'geofence_name' => 'জিওফেন্সের নাম',
        'tail_color' => 'লেজ রঙ',
        'tail_length' => 'লেজ দৈর্ঘ্য',
        'engine_hours' => 'ইঞ্জিন সময়',
        'detect_engine' => 'দ্বারা ইঞ্জিন চালু / বন্ধ সনাক্ত করুন',
        'min_moving_speed' => 'নূন্যতম। কিমি / ঘন্টা গতিবেগ চলমান',
        'min_fuel_fillings' => 'নূন্যতম। জ্বালানী পূরণগুলি সনাক্ত করতে জ্বালানী পার্থক্য',
        'min_fuel_thefts' => 'নূন্যতম। জ্বালানী চুরি সনাক্ত করতে জ্বালানী পার্থক্য',
        'expiration_by' => 'দ্বারা মেয়াদ উত্তীর্ণ',
        'interval' => 'অন্তর',
        'last_service' => 'শেষ পরিষেবা',
        'trigger_event_left' => 'ট্রিগার ইভেন্ট যখন ছেড়ে যায়',
        'current_odometer' => 'কারেন্ট ওডোমিটার',
        'current_engine_hours' => 'বর্তমান ইঞ্জিন সময়',
        'renew_after_expiration' => 'মেয়াদ শেষ হওয়ার পরে পুনর্নবীকরণ করুন',
        'sms_template_id' => 'এসএমএস টেম্পলেট',
        'frequency' => 'ফ্রিকোয়েন্সি',
        'unit' => 'ইউনিট',
        'noreply_email' => 'কোন উত্তর ইমেল ঠিকানা',
        'signature' => 'স্বাক্ষর',
        'use_smtp_server' => 'এসএমটিপি সার্ভার ব্যবহার করুন',
        'smtp_server_host' => 'এসএমটিপি সার্ভার হোস্ট',
        'smtp_server_port' => 'এসএমটিপি সার্ভার পোর্ট',
        'smtp_security' => 'এসএমটিপি সুরক্ষা',
        'smtp_username' => 'এসএমটিপি ব্যবহারকারীর নাম',
        'smtp_password' => 'এসএমটিপি পাসওয়ার্ড',
        'from_name' => 'নাম থেকে',
        'icons' => 'আইকন',
        'server_name' => 'সার্ভার নাম',
        'available_maps' => 'উপলব্ধ মানচিত্র',
        'default_language' => 'নির্ধারিত ভাষা',
        'default_timezone' => 'ডিফল্ট টাইমজোন',
        'default_unit_of_distance' => 'দূরত্বের ডিফল্ট ইউনিট',
        'default_unit_of_capacity' => 'সক্ষমতা ডিফল্ট ইউনিট',
        'default_unit_of_altitude' => 'উচ্চতার ডিফল্ট ইউনিট',
        'default_date_format' => 'ডিফল্ট তারিখের ফর্ম্যাট',
        'default_time_format' => 'ডিফল্ট সময় বিন্যাস',
        'default_map' => 'ডিফল্ট মানচিত্র',
        'default_object_online_timeout' => 'ডিফল্ট অবজেক্ট অনলাইন টাইমআউট',
        'logo' => 'লোগো',
        'login_page_logo' => 'লগইন পৃষ্ঠা লোগো',
        'frontpage_logo' => 'ফ্রন্টপেজ লোগো',
        'favicon' => 'ফ্যাভিকন',
        'allow_users_registration' => 'ব্যবহারকারীদের নিবন্ধকরণের অনুমতি দিন',
        'default_maps' => 'ডিফল্ট মানচিত্র',
        'subscription_expiration_after_days' => 'সাবস্ক্রিপশন কয়েক দিন পরে',
        'gprs_template_id' => 'জিপিআরএস টেম্পলেট',
        'calibrations' => 'ক্রমাঙ্কন',
        'ftp_server' => 'এফটিপি সার্ভার',
        'ftp_port' => 'এফটিপি বন্দর',
        'ftp_username' => 'এফটিপি ব্যবহারকারীর নাম',
        'ftp_password' => 'এফটিপি পাসওয়ার্ড',
        'ftp_path' => 'এফটিপি পাথ',
        'period' => 'পিরিয়ড',
        'hour' => 'আওয়ার',
        'color' => 'রঙ',
        'polyline' => 'রুট',
        'request_method' => 'অনুরোধ পদ্ধতি',
        'authentication' => 'প্রমাণীকরণ',
        'username' => 'ব্যবহারকারীর নাম',
        'encoding' => 'এনকোডিং',
        'time_adjustment' => 'সময় সমন্বয়',
        'parameter' => 'প্যারামিটার',
        'export_type' => 'রফতানির ধরণ',
        'groups' => 'দল',
        'file' => 'ফাইল',
        'extra' => 'অতিরিক্ত',
        'parameter_value' => 'প্যারামিটার মান',
        'enable_plans' => 'পরিকল্পনা সক্ষম করুন',
        'payment_type' => 'শোধের ধরণ',
        'paypal_client_id' => 'পেপাল ক্লায়েন্ট আইডি',
        'paypal_secret' => 'পেপাল গোপন',
        'paypal_currency' => 'পেপাল মুদ্রা',
        'paypal_payment_name' => 'পেপ্যাল পেমেন্টের নাম',
        'objects' => 'অবজেক্টস',
        'duration_value' => 'সময়কাল',
        'permissions' => 'অনুমতি',
        'plan' => 'পরিকল্পনা',
        'default_billing_plan' => 'ডিফল্ট বিলিং পরিকল্পনা',
        'sensor_group_id' => 'সেন্সর গ্রুপ',
        'daylight_saving_time' => 'দিবালোক সাশ্রয়ের সময়',
        'phone_number' => 'ফোন নম্বর',
        'action' => 'কর্ম',
        'time' => 'সময়',
        'order' => 'অর্ডার',
        'geocoder_api' => 'জিওকোডার এপিআই',
        'geocoder_cache' => 'জিওকোডার ক্যাশে',
        'geocoder_cache_days' => 'জিওকোডার ক্যাশে দিন',
        'geocoder_cache_delete' => 'জিওকোডার ক্যাশে মুছুন',
        'api_key' => 'এপিআই কী',
        'api_url' => 'API url',
        'map_center_latitude' => 'মানচিত্র কেন্দ্র অক্ষাংশ',
        'map_center_longitude' => 'মানচিত্র কেন্দ্র দ্রাঘিমাংশ',
        'map_zoom_level' => 'মানচিত্র জুম স্তর',
        'dst_type' => 'প্রকার',
        'provider' => 'প্রদানকারী',
        'week_start_day' => 'ডিফল্ট ক্যালেন্ডার সপ্তাহ শুরুর দিন',
        'ip' => 'আইপি',
        'gprs_templates_only' => 'জিপিআরএস টেম্পলেটগুলি কেবলমাত্র আদেশগুলি দেখান',
        'select_all_objects' => 'সমস্ত বস্তু নির্বাচন করুন',
        'icon_type' => 'আইকন টাইপ',
        'on_setflag_1' => 'শুরু চরিত্র',
        'on_setflag_2' => 'অক্ষরের পরিমাণ',
        'on_setflag_3' => 'প্যারামিটারের মান',
        'domain' => 'ডোমেইন',
        'auth_id' => 'প্রমাণ আইডি',
        'auth_token' => 'আথ টোকেন',
        'senders_phone' => 'প্রেরকের ফোন নম্বর',
        'database_clear_status' => 'স্বয়ংক্রিয় ইতিহাস পরিষ্কার',
        'database_clear_days' => 'দিন রাখতে হবে',
        'ignition_detection' => 'দ্বারা ইগনিশন সনাক্তকরণ',
        'here_map_id' => 'HERE.com অ্যাপ আইডি',
        'here_map_code' => 'HERE.com অ্যাপ কোড',
        'login_page_panel_background_color' => 'লগইন পৃষ্ঠা প্যানেল ব্যাকগ্রাউন্ড রঙ',
        'login_page_panel_transparency' => 'লগইন পৃষ্ঠা প্যানেল স্বচ্ছতা',
        'visible' => 'দৃশ্যমান',
        'template_color' => 'টেমপ্লেটের রঙ',
        'background' => 'পটভূমি',
        'login_page_text_color' => 'লগইন পৃষ্ঠার পাঠ্য রঙ',
        'login_page_background_color' => 'লগইন পৃষ্ঠা ব্যাকগ্রাউন্ড রঙ',
        'welcome_text' => 'স্বাগতম পাঠ্য',
        'bottom_text' => 'নিচের লিখা',
        'apple_store_link' => 'অ্যাপল স্টোর লিঙ্ক',
        'google_play_link' => 'গুগল প্লে লিঙ্ক',
        'position' => 'অবস্থান',
        'stop_duration_longer_than' => 'এর চেয়ে বেশি সময় বন্ধ করুন',
        'mapbox_access_token' => 'ম্যাপবক্স অ্যাক্সেস টোকেন',
        'flag' => 'পতাকা',
        'shift_start' => 'শিফট শুরু',
        'shift_finish' => 'শিফট সমাপ্তি',
        'shift_start_tolerance' => 'শিফট শুরু সহনশীলতা',
        'shift_finish_tolerance' => 'শিফট ফিনিস সহনশীলতা',
        'excessive_exit' => 'অতিরিক্ত প্রস্থান',
        'smtp_authentication' => 'এসএমটিপি প্রমাণীকরণ',
        'skip_calibration' => 'ক্রমাঙ্কন পরিসীমা বাইরে গণনা বাদ দিন',
        'bing_maps_key' => 'বিং মানচিত্র কী',
        'stripe_public_key' => 'স্ট্রিপ পাবলিক কী',
        'stripe_secret_key' => 'স্ট্রিপ গোপন কী',
        'stripe_currency' => 'স্ট্রিপ মুদ্রা',
        'priority' => 'অগ্রাধিকার',
        'pickup_address' => 'পিক আপের ঠিকানা',
        'delivery_address' => 'সরবরাহের ঠিকানা',
        'schedule' => 'সময়সূচী',
        'sound_notification' => 'শব্দ বিজ্ঞপ্তি',
        'push_notification' => 'ধাক্কা বিজ্ঞপ্তি',
        'email_notification' => 'ইমেলের বিজ্ঞপ্তি',
        'sms_notification' => 'এসএমএস বিজ্ঞপ্তি',
        'webhook_notification' => 'ওয়েবহুক বিজ্ঞপ্তি',
        'offline_duration_longer_than' => 'এর চেয়ে অফলাইন সময়কাল',
        'sms_gateway_headers' => 'এসএমএস গেটওয়ে শিরোনাম',
        'forward' => 'ফরোয়ার্ড',
        'by_status' => 'স্ট্যাটাসে',
        'icon_status_online' => 'অনলাইন স্থিতি আইকন',
        'icon_status_offline' => 'অফলাইন স্থিতি আইকন',
        'icon_status_ack' => 'আক স্থিতি আইকন',
        'icon_status_engine' => 'ইঞ্জিনের স্থিতি আইকন',
        'server_description' => 'সার্ভারের বিবরণ',
        'bypass_invalid' => 'অবৈধ স্থানাঙ্কের অনুমতি দিন',
        'installation_date' => 'ইনস্টলেশন তারিখ',
        'sim_activation_date' => 'সিম অ্যাক্টিভেশন তারিখ',
        'sim_expiration_date' => 'সিমের মেয়াদ শেষ হওয়ার তারিখ',
        'activation_date' => 'সক্রিয়করণের তারিখ',
        'secret_key' => 'গোপন চাবি',
        'currency' => 'মুদ্রা',
        'client_id' => 'ক্লায়েন্ট আইডি',
        'secret' => 'গোপন',
        'payment_name' => 'পেমেন্টের নাম',
        'merchant_id' => 'মার্চেন্ট আইডি',
        'public_key' => 'পাবলিক কী',
        'private_key' => 'ব্যক্তিগত কী',
        'braintree_plan_ids' => 'সার্ভার পরিকল্পনার জন্য ব্র্যান্ট্রি প্ল্যান আইডি',
        'braintree_plan_explanation' => 'আপনাকে অবশ্যই ব্রিন্ট্রি কন্ট্রোল প্যানেলে পুনরুদ্ধারকারী বিলিং পরিকল্পনা তৈরি করতে হবে, আপনার সার্ভারে বিলিং পরিকল্পনাটি সম্পর্কিত আইডিটি এখানে নির্বাচন করুন।',
        'braintree_merchant_explanation' => 'আপনাকে অবশ্যই ব্রিন্ট্রি কন্ট্রোল প্যানেলে মার্চেন্ট অ্যাকাউন্ট তৈরি করতে হবে এবং আইডিটি এখানে প্রবেশ করতে হবে।',
        'braintree_currency_match' => 'মার্চেন্ট অ্যাকাউন্টের মুদ্রা অবশ্যই প্ল্যান মুদ্রার সাথে মেলে',
        'merchant_account_id' => 'মার্চেন্ট একাউন্ট আইডি',
        'master_key' => 'প্রধান চাবি',
        'token' => 'টোকেন',
        'paydunya_currency_warning' => 'একমাত্র উপলভ্য মুদ্রা এফসিএফএ। আপনি যদি এটি কনফিগার করেন তবে দয়া করে নিশ্চিত হন যে আপনার অন্যান্য অর্থ প্রদান একই মুদ্রার সাথে মিলেছে। অন্যথায় ব্যবহারকারীরা বিভিন্ন পরিকল্পনা সহ একই পরিকল্পনা কিনে দেওয়ার সুযোগ পাবেন।',
        'country' => 'দেশ',
        'merchant_account' => 'বণিক অ্যাকাউন্ট',
        'origin_key' => 'মূল কী',
        'test_config' => 'পরীক্ষা কনফিগার',
        'environment' => 'পরিবেশ',
        'three_letter_iso' => 'তিন-বর্ণের আইএসও মুদ্রার কোড',
        'google_maps_key' => 'গুগল মানচিত্রের এপি কী',
        'maptiler_key' => 'মানচিত্রার কী',
        'streetview_api' => 'রাস্তার দৃশ্য API',
        'streetview_key' => 'স্ট্রিটভিউ এপিআই কী',
        'openmaptiles_url' => 'ওপেনম্যাপটাইজস url',
        'unit_cost' => 'ইউনিট খরচ',
        'supplier' => 'সরবরাহকারী',
        'buyer' => 'ক্রেতা',
        'expense_type' => 'ব্যয়ের ধরণ',
        'device_cameras_days' => 'ডিভাইস ক্যামেরার চিত্রগুলি রাখার দিনগুলি',
        'api_app_id' => 'অ্যাপ আইডি',
        'api_app_code' => 'অ্যাপ কোড',
        'api_app_secret' => 'অ্যাপ গোপন',
        'invoice_number' => 'চালান নম্বর',
        'one_time_payment' => 'এক সময় পেমেন্ট',
        'sharing_id' => 'ভাগ করে নেওয়া',
        'idle_duration_longer_than' => 'অলস সময়কাল এর চেয়ে বেশি',
        'delete_after_expiration' => 'মেয়াদ শেষ হওয়ার পরে মুছুন',
        'sharing_email' => 'ভাগ করার লিঙ্ক সহ ইমেল বিজ্ঞপ্তি',
        'sharing_sms' => 'লিঙ্ক ভাগ করে সঙ্গে এসএমএস বিজ্ঞপ্তি',
        'sms' => 'খুদেবার্তা',
        'template' => 'টেমপ্লেট',
        'commands' => 'কমান্ড',
        'brand' => 'ডিভাইস প্রস্তুতকারক',
        'model' => 'মডেল',
        'apn_name' => 'এপিএন নাম',
        'apn_username' => 'এপিএন ব্যবহারকারীর নাম',
        'apn_password' => 'এপিএন পাসওয়ার্ড',
        'ignition_duration_longer_than' => 'এর চেয়ে ইগনিশন সময়কাল',
        'tasks' => 'কাজ',
        'id' => 'আইডি',
        'columns' => 'কলাম',
        'called_at' => 'এ কল',
        'alert_type' => 'সতর্কতার ধরণ',
        'response' => 'প্রতিক্রিয়া',
        'remarks' => 'মন্তব্য',
        'client' => 'ক্লায়েন্ট',
        'event_type' => 'ইভেন্টের ধরণ',
        'data_type' => 'ডেটা টাইপ',
        'slug' => 'স্লাগ',
        'required' => 'প্রয়োজনীয়',
        'validation' => 'বৈধতা',
        'text' => 'পাঠ্য',
        'datetime' => 'তারিখ সময়',
        'boolean' => 'বুলিয়ান',
        'select' => 'নির্বাচন করুন',
        'multiselect' => 'বহুগুণ',
        'options' => 'বিকল্পগুলি',
        'option' => 'বিকল্প',
        'default' => 'ডিফল্ট',
        'msisdn' => 'এমএসআইএসডিএন',
        'notes' => 'মন্তব্য',
        'skip_empty' => 'খালি মান ছেড়ে যান',
        'distance_limit' => 'দূরত্বের সীমা',
        'distance_tolerance' => 'দূরত্ব সহনশীলতা',
        'pois' => 'পিওআই',
        'device_type_id' => 'ডিভাইসের ধরন',
        'custom_fields' => 'অস্ত্রোপচার',
        'device_name' => 'ডিভাইসের নাম',
        'auto_hide_notification' => 'পটআপটি স্বয়ংক্রিয়ভাবে লুকান',
        'continuous_duration' => 'অবিচ্ছিন্ন সময়কাল',
        'captcha_provider' => 'ক্যাপচ্যা সরবরাহকারী',
        'google_recaptcha' => 'গুগল রেক্যাপচা',
        'recaptcha_site_key' => 'ReCAPTCHA সাইট কী',
        'recaptcha_secret_key' => 'রেক্যাপচা গোপন কী',
        'g-recaptcha-response' => 'ReCAPTCHA',
        'here_api_key' => 'HERE.com এপিআই কী',
        'time_duration' => 'সময়কাল',
        'offset' => 'অফসেট',
        'geofence_device' => 'যন্ত্র',
        'webhook_key' => 'ওয়েবহুক কী',
        'skip_blank_results' => 'ফাঁকা ফলাফল এড়িয়ে যান',
        'account_sid' => 'অ্যাকাউন্ট SID',
        'speed_limit_tolerance' => 'গতি সীমা সহনশীলতা',
        'started_at' => 'সময় শুরু',
        'ended_at' => 'শেষ সময়',
        'region' => 'অঞ্চল',
        'adapted' => 'অভিযোজিত',
        'silent_notification' => 'কয়েক মিনিটের মধ্যে পুনরাবৃত্তি হলে বিজ্ঞপ্তি উপেক্ষা করুন',
        'tomtom_key' => 'টমটম কী',
        'authorized' => 'অনুমোদিত',
        'email_verification' => 'ইমেইলের সত্যতা যাচাই',
        'project_id' => 'প্রকল্প আইডি',
        'project_psw' => 'প্রকল্পের পাসওয়ার্ড',
        'verify_id' => 'আইডি যাচাই করুন',
        'app_tracker_login' => 'ট্র্যাকার অ্যাপ লগইন সক্ষম করা হয়েছে৷',
        'merchant_code' => 'বণিক কোড',
        'count' => 'গণনা',
        'detect_distance' => 'দ্বারা দূরত্ব সনাক্তকরণ',
        'detect_speed' => 'দ্বারা গতি সনাক্তকরণ',
        'routes' => 'রুট',
        'battery_threshold' => 'ব্যাটারি থ্রেশহোল্ড',
        'state' => 'রাষ্ট্র',
        'duration' => 'সময়কাল',
        'statuses' => 'স্ট্যাটাস',
        'first_name' => 'নামের প্রথম অংশ',
        'last_name' => 'নামের শেষাংশ',
        'personal_code' => 'ব্যক্তিগত কোড',
        'birth_date' => 'জন্ম তারিখ',
        'company_id' => 'প্রতিষ্ঠান',
        'registration_code' => 'নিবন্ধন কোড',
        'vat_number' => 'ভ্যাট নম্বর',
        'address' => 'ঠিকানা',
        'comment' => 'মন্তব্য করুন',
        'duration_format' => 'সময়কাল বিন্যাস',
        'default_duration_format' => 'ডিফল্ট সময়কাল বিন্যাস',
        'login_token' => 'টোকেন',
        'monthly' => 'মাসিক',
        'amount' => 'পরিমাণ',
        'bad_sms_gateway_url' => 'খারাপ SMS গেটওয়ে ইউআরএল বা কনফিগারেশন',
        'rates' => 'হার',
        'fields' => 'ক্ষেত্র',
        'tenant_id' => 'ভাড়াটে আইডি',
        'client_secret' => 'ক্লায়েন্ট গোপন',
        'default_login_methods' => 'ডিফল্ট লগইন পদ্ধতি',
        'forwards' => 'ফরোয়ার্ড',
        'detection_speed' => 'সনাক্তকরণ গতি',
        'detach_on_no_position_data' => 'কোনো অবস্থানের ডেটাতে বিচ্ছিন্ন করুন',
        'extra_expiration_time' => 'অতিরিক্ত মেয়াদ শেষ হওয়ার সময়',
        'fuel_detect_sec_after_stop' => 'থামার পরে জ্বালানী পরিবর্তন সনাক্ত করুন',
        'login_periods' => 'লগইন সময়কাল',
    ),
    'same_protocol' => 'ডিভাইসগুলি অবশ্যই একই প্রোটোকলের হতে হবে।',
    'contains' => ':attribute :value থাকতে হবে।',
    'ip_port' => ':attribute :PORT ফরমেটের সাথে মেলে না',
    'required_unless' => 'এই :attribute ক্ষেত্র প্রয়োজন।',
    'translation_file' => 'অনুবাদ ফাইল বিদ্যমান নেই',
    'placeholder' => 'বৈশিষ্ট্য &quot; :placeholder &quot; অনুপস্থিত',
    'image_valid' => ':attribute অবশ্যই একটি চিত্র হতে হবে।',
    'missing_configuration_value' => 'অনুপস্থিত :attribute কনফিগারেশন মান।',
    'sms_gateway_error' => 'বার্তা পাঠানো যায় না। এসএমএস গেটওয়ে ত্রুটি।',
    'cant_configure_device' => 'ডিভাইস কনফিগার করতে পারেনি',
    'field_does_not_exist' => 'ক্ষেত্র :attribute বিদ্যমান নেই',
    'unsupported_rules' => 'অসমর্থিত নিয়ম:',
    'unsupported_parameterized_rules' => 'অসমর্থিত প্যারামিটারাইজড বিধিগুলি:',
    'cant_update_custom_field' => ':field &quot; আপডেট করতে পারে না কারণ এই কাস্টম ক্ষেত্রটি ব্যবহার করে বিদ্যমান রেকর্ড রয়েছে',
    'strong_password' => 'শক্তিশালী গুপ্তমন্ত্র',
    'uppercase_character' => 'বড় হাতের অক্ষর প্রয়োজন',
    'lowercase_character' => 'ছোট হাতের অক্ষর প্রয়োজন',
    'digit_character' => 'অঙ্কের অক্ষর প্রয়োজন',
    'wrong_captcha' => 'ভুল ক্যাপচা',
    'dimensions' => ':attribute অবৈধ চিত্রের মাত্রা রয়েছে।',
    'mimetypes' => ':attribute অবশ্যই টাইপের একটি ফাইল হতে হবে :values ।',
    'in_array' => ':attribute মাঠে অস্তিত্ব নেই :other ।',
    'uploaded' => ':attribute আপলোড করতে ব্যর্থ হয়েছে। এটা সম্ভব যে সার্ভার এই ধরনের আকার গ্রহণ করে না।',
    'login_methods'  => array(
        'email' => 'ইমেইল',
        'azure' => 'মাইক্রোসফট Azure',
    ),
    'host' => ':attribute বৈধ হোস্ট নয়',
    'host_port' => ':attribute বিন্যাস HOST :PORT সাথে মেলে না',
    'unique_table_msg' => ':attribute টি ইতিমধ্যেই :table এ নেওয়া হয়েছে',
);