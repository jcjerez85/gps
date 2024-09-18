<?php

return array(

    'accepted' => ':attribute должен быть принят.',
    'active_url' => ':attribute не является допустимым URL-адресом.',
    'after' => ':attribute должна быть дата после :date.',
    'alpha' => ':attribute может содержать только буквы.',
    'alpha_dash' => ':attribute может содержать только буквы, цифры и тире.',
    'alpha_num' => ':attribute может содержать только буквы и цифры.',
    'array' => ':attribute должен быть массивом.',
    'before' => ':attribute должен быть дата до :date.',
    'between'  => array(
        'numeric' => ':attribute должен быть между :min и :max.',
        'file' => ':attribute должен быть между :min и :max Кб.',
        'string' => ':attribute должен быть между :min и :max символами.',
        'array' => ':attribute должен быть между :min и :max.',
    ),
    'confirmed' => ':attribute подтверждение не соответствует.',
    'date' => ':attribute не является действительной датой.',
    'date_format' => ':attribute не соответствует формату :format.',
    'different' => ':attribute и :other должны быть разными.',
    'digits' => ':attribute должно быть :digits цифры.',
    'digits_between' => ':attribute должен быть между :min и :max цифрой.',
    'email' => ':attribute должен быть действительным e-mail.',
    'exists' => 'Выбранный :attribute является недействительным.',
    'image' => ':attribute должно быть изображение.',
    'in' => 'Выбранный :attribute является недействительным.',
    'integer' => ':attributeдолжен быть целым числом.',
    'ip' => ':attribute должен быть действительным IP-адресом.',
    'max'  => array(
        'numeric' => ':attribute может быть не больше :max.',
        'file' => ':attribute может быть не больше :max Кб.',
        'string' => ':attribute может быть не больше :max символов.',
        'array' => ':attribute может не быть больше, чем :max.',
    ),
    'mimes' => ':attribute должен быть файлом типа: :values.',
    'min'  => array(
        'numeric' => ':attribute должен быть не менее :min.',
        'file' => ':attribute должен быть не менее :min Кб.',
        'string' => ':attribute должен быть не менее :min символов.',
        'array' => ':attribute должен иметь по крайней мере :min.',
    ),
    'not_in' => 'Выбранный :attribute не верен.',
    'numeric' => ':attribute должен быть числом.',
    'regex' => ':attribute формат недействителен.',
    'required' => ':attribute обязательно для заполнения.',
    'required_if' => ':attribute обязательно для заполнения.',
    'required_with' => ':attribute поле требуется, когда :values присутствует.',
    'required_with_all' => ':attribute поле требуется, когда :values присутствует.',
    'required_without' => ':attribute поле требуется, когда :values не присутствует.',
    'required_without_all' => ':attribute поле требуется, если ни один из :values присутствуют.',
    'same' => ':attribute и :other должны соответствовать.',
    'size'  => array(
        'numeric' => ':attribute должен быть :size.',
        'file' => ':attribute должен быть :size Кб.',
        'string' => ':attribute должен быть :size символов.',
        'array' => ':attribute должен содержать :size.',
    ),
    'unique' => 'The :attribute уже был взят.',
    'url' => 'The :attribute формат недействителен.',
    'array_max' => ':attribute макс. предметы :max.',
    'lesser_than' => ':attribute должен быть меньше :other',
    'custom'  => array(
        'attribute-name'  => array(
            'rule-name' => 'custom-message',
        ),
        'frontpage_logo'  => array(
            'dimensions' => 'Максимальная высота логотипа на главной странице - 60 пикселей.',
        ),
        'favicon'  => array(
            'dimensions' => 'Размер значка должен составлять 16 на 16 пикселей.',
        ),
    ),
    'attributes'  => array(
        'email' => 'E-mail',
        'password' => 'Пароль',
        'password_confirmation' => 'Подтверждение пароля',
        'remember_me' => 'Запомнить меня',
        'name' => 'Имя',
        'imei' => 'IMEI',
        'imei_device' => 'IMEI или идентификатор устройства',
        'fuel_measurement_type' => 'Измерение топлива',
        'fuel_cost' => 'Стоимость топлива',
        'icon_id' => 'Иконка устройства',
        'active' => 'Активный',
        'polygon_color' => 'Фоновый цвет',
        'devices' => 'Приборы',
        'geofences' => 'Геозоны',
        'overspeed' => 'Превышение скорости',
        'fuel_consumption' => 'Потребление топлива',
        'description' => 'Описание',
        'map_icon_id' => 'Иконка маркера',
        'coordinates' => 'Пункт карты',
        'date_from' => 'Дата с',
        'date_to' => 'Дата по',
        'code' => 'Код',
        'title' => 'Заглавие',
        'note' => 'Содержание',
        'path' => 'Файл',
        'period_name' => 'Название периода',
        'days' => 'Дней',
        'devices_limit' => 'Ограничение устройств',
        'trial' => 'Пробный',
        'price' => 'Цена',
        'message' => 'Сообщение',
        'tag' => 'Параметр',
        'timezone_id' => 'Часовой пояс',
        'unit_of_distance' => 'Единица расстояния',
        'unit_of_capacity' => 'Единица мощности',
        'unit_of_altitude' => 'Единица высоты',
        'user' => 'Пользователь',
        'group_id' => 'Группа',
        'permission_to_add_devices' => 'Добавить устройства раз два',
        'sms_gateway_url' => 'URL-адрес шлюза SMS',
        'mobile_phone' => 'Мобильный телефон',
        'permission_to_use_sms_gateway' => 'Шлюз SMS',
        'loged_at' => 'Последний вход',
        'manager_id' => 'Менеджер',
        'sim_number' => 'Номер SIM-карты',
        'device_model' => 'Модель устройства',
        'rfid' => 'RFID',
        'phone' => 'Телефон',
        'device_id' => 'Устройство',
        'tag_value' => 'Значение параметра',
        'device_port' => 'Порт устройства',
        'event' => 'События',
        'port' => 'Порт',
        'device_protocol' => 'Протокол устройства',
        'protocol' => 'Протокол',
        'sensor_name' => 'Имя датчика',
        'sensor_type' => 'Тип датчика',
        'sensor_template' => 'Шаблон датчика',
        'tag_name' => 'Название параметра',
        'min_value' => 'Мин. значение',
        'max_value' => 'Макс. значение',
        'on_value' => 'ВКЛ',
        'off_value' => 'ВЫКЛ',
        'shown_value_by' => 'Показать значение по',
        'full_tank_value' => 'Значение параметра',
        'formula' => 'Формула',
        'parameters' => 'Параметры',
        'full_tank' => 'Полный бак в литрах / галлонах',
        'fuel_tank_name' => 'Название топливного бака',
        'odometer_value' => 'Стоимость',
        'odometer_value_by' => 'Одометр',
        'unit_of_measurement' => 'Единица измерения',
        'plate_number' => 'Номерной знак',
        'vin' => 'VIN',
        'registration_number' => 'Регистрационный номер / номер основного средства',
        'object_owner' => 'Владелец / менеджер объекта',
        'additional_notes' => 'Дополнительные замечания',
        'expiration_date' => 'Истечения срока действия',
        'days_to_remind' => 'Дни напоминания до истечения срока действия',
        'type' => 'Тип',
        'format' => 'Формат',
        'show_addresses' => 'Показать адреса',
        'stops' => 'Остановки',
        'speed_limit' => 'Ограничение скорости',
        'zones_instead' => 'Геозоны вместо адресов',
        'daily' => 'Ежедневно',
        'weekly' => 'Еженедельно',
        'send_to_email' => 'Отправить на e-mail',
        'filter' => 'Фильтр',
        'status' => 'Статус',
        'date' => 'Дата',
        'geofence_name' => 'Название геозоны',
        'tail_color' => 'Цвет остатка',
        'tail_length' => 'Длина остатка',
        'engine_hours' => 'Моточасы',
        'detect_engine' => 'Выявить состояние двигателя ВКЛ/ВЫКЛ',
        'min_moving_speed' => 'Мин. скорость движения в км/ч',
        'min_fuel_fillings' => 'Минимум разниц топлива для определения заправки',
        'min_fuel_thefts' => 'Минимум разниц топлива для обнаружения краж',
        'expiration_by' => 'Истечение срока',
        'interval' => 'Интервал',
        'last_service' => 'Последнее обслуживание',
        'trigger_event_left' => 'Вызвать действие при остатке',
        'current_odometer' => 'Текущий одометр',
        'current_engine_hours' => 'Текущее моточасы',
        'renew_after_expiration' => 'Обновить после истечения срока',
        'sms_template_id' => 'СМС шаблон',
        'frequency' => 'Частота',
        'unit' => 'Единица',
        'noreply_email' => 'Email без ответа',
        'signature' => 'Подпись',
        'use_smtp_server' => 'Используйте SMTP сервер',
        'smtp_server_host' => 'Хост SMTP сервера',
        'smtp_server_port' => 'Порт SMTP сервера',
        'smtp_security' => 'Безопасность SMTP',
        'smtp_username' => 'Имя пользователя SMTP',
        'smtp_password' => 'Пароль SMTP',
        'from_name' => 'От имени',
        'icons' => 'Иконки',
        'server_name' => 'Имя сервера',
        'available_maps' => 'Карты в наличии',
        'default_language' => 'Язык по умолчанию',
        'default_timezone' => 'Временная зона по умолчанию',
        'default_unit_of_distance' => 'Единица измерения длины по умолчанию',
        'default_unit_of_capacity' => 'Единица измерения производительности по умолчанию',
        'default_unit_of_altitude' => 'Единица высоты по умолчанию',
        'default_date_format' => 'Формат даты по умолчанию',
        'default_time_format' => 'Формат времени по умолчанию',
        'default_map' => 'Карта по умолчанию',
        'default_object_online_timeout' => 'Онлайн перерыв обьекта по умолчанию',
        'logo' => 'Логотип',
        'login_page_logo' => 'Логотип страницы входа',
        'frontpage_logo' => 'Логотип главной страницы',
        'favicon' => 'Favicon',
        'allow_users_registration' => 'Разрешить регистрацию пользователей',
        'default_maps' => 'Карты по умолчанию',
        'subscription_expiration_after_days' => 'Срок действия подписки дней',
        'gprs_template_id' => 'GPRS шаблон',
        'calibrations' => 'Калибровка',
        'ftp_server' => 'FTP сервер',
        'ftp_port' => 'FTP порт',
        'ftp_username' => 'FTP имя пользователя',
        'ftp_password' => 'FTP пароль',
        'ftp_path' => 'FTP путь',
        'period' => 'Период',
        'hour' => 'Час',
        'color' => 'Цвет',
        'polyline' => 'Маршрут',
        'request_method' => 'Метод запроса',
        'authentication' => 'Идентификация',
        'username' => 'Имя пользователя',
        'encoding' => 'Кодирование',
        'time_adjustment' => 'Регулировка времени',
        'parameter' => 'Параметры',
        'export_type' => 'Тип выгрузки',
        'groups' => 'Группы',
        'file' => 'Файл',
        'extra' => 'Дополнительный',
        'parameter_value' => 'Значение параметра',
        'enable_plans' => 'Включить планы',
        'payment_type' => 'Тип оплаты',
        'paypal_client_id' => 'ID клиента Paypal',
        'paypal_secret' => 'Код Paypal',
        'paypal_currency' => 'Валюта Paypal',
        'paypal_payment_name' => 'Имя платежа Paypal',
        'objects' => 'Объекты',
        'duration_value' => 'Продолжительность',
        'permissions' => 'Права доступа',
        'plan' => 'План',
        'default_billing_plan' => 'План выставления счетов по умолчани',
        'sensor_group_id' => 'Группа датчиков',
        'daylight_saving_time' => 'Летнее время',
        'phone_number' => 'Номер телефона',
        'action' => 'Действие',
        'time' => 'Время',
        'order' => 'Заказ',
        'geocoder_api' => 'API uеокодера',
        'geocoder_cache' => 'Кэш геокодера',
        'geocoder_cache_days' => 'Время кэширования геокодирования',
        'geocoder_cache_delete' => 'Удалить кеш геокодера',
        'api_key' => 'API ключ',
        'api_url' => 'API url',
        'map_center_latitude' => 'Широта центра карты',
        'map_center_longitude' => 'Долгота центра карты',
        'map_zoom_level' => 'Уровень масштабирования карты',
        'dst_type' => 'Тип',
        'provider' => 'Поставщик',
        'week_start_day' => 'Начальный день недели календаря по умолчанию',
        'ip' => 'IP',
        'gprs_templates_only' => 'Показывать только команды шаблонов GPRS',
        'select_all_objects' => 'Выбрать все объекты',
        'icon_type' => 'Тип иконки',
        'on_setflag_1' => 'Начальный символ',
        'on_setflag_2' => 'Количество символов',
        'on_setflag_3' => 'Значение параметра',
        'domain' => 'Домен',
        'auth_id' => 'ID идентификации',
        'auth_token' => 'Токен идентификации',
        'senders_phone' => 'Телефон отправителя',
        'database_clear_status' => 'Автоматическая очистка истории',
        'database_clear_days' => 'Дни для сохранения',
        'ignition_detection' => 'Выявление вкл зажигания',
        'template_color' => 'Цвет шаблона',
        'background' => 'Фон',
        'login_page_text_color' => 'Цвет текста страницы входа',
        'login_page_background_color' => 'Цвет фона страницы входа',
        'welcome_text' => 'Приветственный текст',
        'bottom_text' => 'Нижний текст',
        'apple_store_link' => 'Ссылка на Apple Store',
        'google_play_link' => 'Ссылка на Google play',
        'here_map_id' => 'ID приложения HERE.com',
        'here_map_code' => 'Код приложения HERE.com',
        'login_page_panel_background_color' => 'Цвет фона панели страницы входа',
        'login_page_panel_transparency' => 'Прозрачность панели страницы входа',
        'visible' => 'Видимый',
        'position' => 'Должность',
        'stop_duration_longer_than' => 'Длительность остановки дольше, чем',
        'mapbox_access_token' => 'Маркер доступа MapBox',
        'flag' => 'Флаг',
        'shift_start' => 'Начало смены',
        'shift_finish' => 'Сдвиг',
        'shift_start_tolerance' => 'Допуск сдвига',
        'shift_finish_tolerance' => 'Допуск на сдвиг',
        'excessive_exit' => 'Чрезмерный выход',
        'smtp_authentication' => 'Проверка подлинности SMTP',
        'skip_calibration' => 'Исключить вычисления из диапазона калибровки',
        'bing_maps_key' => 'Ключ карт Bing',
        'stripe_public_key' => 'Открытый ключ STRIPE',
        'stripe_secret_key' => 'Секретный ключ STRIPE',
        'stripe_currency' => 'Валюта STRIPE',
        'priority' => 'приоритет',
        'pickup_address' => 'Выберите адрес',
        'delivery_address' => 'Адрес доставки',
        'schedule' => 'График',
        'sound_notification' => 'Звуковое уведомление',
        'push_notification' => 'Отправить уведомление',
        'email_notification' => 'Уведомление по электронной почте',
        'sms_notification' => 'SMS-уведомление',
        'webhook_notification' => 'Уведомление Webhook',
        'offline_duration_longer_than' => 'Автономная продолжительность дольше, чем',
        'sms_gateway_headers' => 'Заголовки шлюзов SMS',
        'forward' => 'Вперед',
        'by_status' => 'По статусу',
        'icon_status_online' => 'Значок статуса онлайн',
        'icon_status_offline' => 'Значок автономного статуса',
        'icon_status_ack' => 'Значок статуса Ack',
        'icon_status_engine' => 'Значок состояния двигателя',
        'server_description' => 'Описание сервера',
        'bypass_invalid' => 'Разрешить неверные координаты',
        'installation_date' => 'Дата установки',
        'sim_activation_date' => 'Дата активации SIM',
        'sim_expiration_date' => 'Срок действия SIM',
        'activation_date' => 'Дата активации',
        'secret_key' => 'Секретный ключ',
        'currency' => 'валюта',
        'client_id' => 'ID клиента',
        'secret' => 'секрет',
        'payment_name' => 'Название платежа',
        'merchant_id' => 'идентификатор продавца',
        'public_key' => 'Открытый ключ',
        'private_key' => 'Закрытый ключ',
        'braintree_plan_ids' => 'Идентификаторы Braintree для серверов',
        'braintree_plan_explanation' => 'Вы должны создать рекуррентный тарифный план в панели управления Braintree, выберите здесь ID соответствующего тарифного плана на своем сервере.',
        'braintree_merchant_explanation' => 'Вы должны создать торговую учетную запись в панели управления Braintree и ввести идентификатор здесь.',
        'braintree_currency_match' => 'Валюта аккаунта продавца должна соответствовать валюте плана',
        'merchant_account_id' => 'Идентификатор торгового счета',
        'master_key' => 'Отмычка',
        'token' => 'знак',
        'paydunya_currency_warning' => 'Единственная доступная валюта - FCFA. Если вы настроите его, убедитесь, что ваши другие платежи соответствуют той же валюте. В противном случае у пользователей будет возможность купить один и тот же тариф по разным ценам.',
        'country' => 'Страна',
        'merchant_account' => 'Торговый счет',
        'origin_key' => 'Ключ происхождения',
        'test_config' => 'Тестовый конфиг',
        'environment' => 'Среда',
        'three_letter_iso' => 'Трехбуквенный код валюты ISO',
        'google_maps_key' => 'Ключ API карт Google',
        'maptiler_key' => 'Ключ MapTiler',
        'streetview_api' => 'API Streetview',
        'streetview_key' => 'Ключ API Streetview',
        'openmaptiles_url' => 'URL-адрес OpenMapTiles',
        'unit_cost' => 'Себестоимость единицы продукции',
        'supplier' => 'поставщик',
        'buyer' => 'Покупатель',
        'expense_type' => 'Тип расходов',
        'device_cameras_days' => 'Дни, чтобы держать изображения камеры устройства',
        'api_app_id' => 'Идентификатор приложения',
        'api_app_code' => 'Код приложения',
        'api_app_secret' => 'Секрет приложения',
        'invoice_number' => 'Номер счета',
        'one_time_payment' => 'Одноразовый платеж',
        'sharing_id' => 'разделение',
        'idle_duration_longer_than' => 'Время простоя дольше чем',
        'delete_after_expiration' => 'Удалить после истечения срока',
        'sharing_email' => 'Уведомление по электронной почте с ссылкой для обмена',
        'sharing_sms' => 'SMS-уведомление с ссылкой для обмена',
        'sms' => 'SMS',
        'template' => 'шаблон',
        'commands' => 'команды',
        'brand' => 'Производитель устройства',
        'model' => 'модель',
        'apn_name' => 'Имя APN',
        'apn_username' => 'APN имя пользователя',
        'apn_password' => 'APN пароль',
        'ignition_duration_longer_than' => 'Продолжительность зажигания больше чем',
        'tasks' => 'Задания',
        'id' => 'Я БЫ',
        'columns' => 'Колонны',
        'called_at' => 'Позвонить в',
        'alert_type' => 'Тип оповещения',
        'response' => 'отклик',
        'remarks' => 'замечания',
        'client' => 'клиент',
        'event_type' => 'Тип события',
        'data_type' => 'Тип данных',
        'slug' => 'слизень',
        'required' => 'необходимые',
        'validation' => 'Проверка',
        'text' => 'Текст',
        'datetime' => 'Datetime',
        'boolean' => 'логический',
        'select' => 'Выбрать',
        'multiselect' => 'Выбор из нескольких вариантов',
        'options' => 'Параметры',
        'option' => 'вариант',
        'default' => 'По умолчанию',
        'msisdn' => 'MSISDN',
        'notes' => 'Ноты',
        'skip_empty' => 'Пропустить пустое значение',
        'distance_limit' => 'Ограничение расстояния',
        'distance_tolerance' => 'Допуск расстояния',
        'pois' => 'POI',
        'device_type_id' => 'Тип устройства',
        'custom_fields' => 'Настраиваемые поля',
        'device_name' => 'Имя устройства',
        'auto_hide_notification' => 'Автоматически скрывать всплывающее окно',
        'continuous_duration' => 'Непрерывная продолжительность',
        'captcha_provider' => 'Провайдер CAPTCHA',
        'google_recaptcha' => 'Google reCAPTCHA',
        'recaptcha_site_key' => 'ключ сайта reCAPTCHA',
        'recaptcha_secret_key' => 'секретный ключ reCAPTCHA',
        'g-recaptcha-response' => 'ReCAPTCHA',
        'here_api_key' => 'HERE.com API-ключ',
        'time_duration' => 'Продолжительность времени',
        'offset' => 'Компенсировать',
        'geofence_device' => 'Устройство',
        'webhook_key' => 'Ключ Webhook',
        'skip_blank_results' => 'Пропустить пустые результаты',
        'account_sid' => 'SID аккаунта',
        'speed_limit_tolerance' => 'Допуск ограничения скорости',
        'started_at' => 'Время начала',
        'ended_at' => 'Время окончания',
        'region' => 'Область, край',
        'adapted' => 'Адаптированный',
        'silent_notification' => 'Игнорировать уведомления, если они повторяются в течение нескольких минут',
        'tomtom_key' => 'Ключ TomTom',
        'authorized' => 'Авторизованный',
        'email_verification' => 'Подтверждение адреса электронной почты',
        'project_id' => 'Идентификатор проекта',
        'project_psw' => 'Пароль проекта',
        'verify_id' => 'Подтвердить идентификатор',
        'app_tracker_login' => 'Вход в приложение Tracker включен',
        'merchant_code' => 'Код продавца',
        'count' => 'Считать',
        'detect_distance' => 'Определение расстояния по',
        'detect_speed' => 'Определение скорости по',
        'routes' => 'Маршруты',
        'battery_threshold' => 'Порог батареи',
        'state' => 'Состояние',
        'duration' => 'Продолжительность',
        'statuses' => 'Статусы',
        'first_name' => 'Имя',
        'last_name' => 'Фамилия',
        'personal_code' => 'Персональный код',
        'birth_date' => 'Дата рождения',
        'company_id' => 'Компания',
        'registration_code' => 'Регистрационный код',
        'vat_number' => 'Номер НДС',
        'address' => 'Адрес',
        'comment' => 'Комментарий',
        'duration_format' => 'Формат продолжительности',
        'default_duration_format' => 'Формат продолжительности по умолчанию',
        'login_token' => 'Токен',
        'monthly' => 'Ежемесячно',
        'amount' => 'Количество',
        'bad_sms_gateway_url' => 'Неверный URL-адрес или конфигурация SMS-шлюза',
        'rates' => 'Ставки',
        'fields' => 'Поля',
        'tenant_id' => 'Идентификатор арендатора',
        'client_secret' => 'Секрет клиента',
        'default_login_methods' => 'Методы входа по умолчанию',
        'forwards' => 'Нападающие',
        'detection_speed' => 'Скорость обнаружения',
        'detach_on_no_position_data' => 'Отключить при отсутствии данных о местоположении',
        'extra_expiration_time' => 'Дополнительное время экспирации',
        'fuel_detect_sec_after_stop' => 'Обнаружение замены топлива после остановки',
        'login_periods' => 'Периоды входа',
    ),
    'same_protocol' => 'Устройства должны быть одного протокола.',
    'contains' => ':attribute должен содержать :value .',
    'ip_port' => ':attribute не соответствует формату IP:PORT',
    'required_unless' => 'Поле :attribute обязательно для заполнения.',
    'translation_file' => 'Файл перевода не существует',
    'placeholder' => 'Атрибут &quot; :placeholder &quot; отсутствует',
    'image_valid' => ':attribute должен быть изображением.',
    'missing_configuration_value' => 'Отсутствует :attribute значение конфигурации :attribute .',
    'sms_gateway_error' => 'Сообщение не может быть отправлено. Ошибка шлюза смс.',
    'cant_configure_device' => 'Не удалось настроить устройство',
    'field_does_not_exist' => '',
    'unsupported_rules' => 'Неподдерживаемые правила:',
    'unsupported_parameterized_rules' => 'Неподдерживаемые параметризованные правила:',
    'cant_update_custom_field' => 'Не удается обновить поле &quot; :field &quot;, так как существуют существующие записи, использующие это настраиваемое поле',
    'strong_password' => 'Надежный пароль',
    'uppercase_character' => 'Требуется заглавная буква',
    'lowercase_character' => 'Требуется строчный символ',
    'digit_character' => 'Требуется цифра',
    'wrong_captcha' => 'Неверная CAPTCHA',
    'dimensions' => ':attribute имеет недопустимые размеры изображения.',
    'mimetypes' => ':attribute должен быть файлом типа :values .',
    'in_array' => 'Поле :attribute не существует в :other .',
    'uploaded' => 'Не удалось загрузить :attribute . Возможно, сервер не принимает такой размер.',
    'login_methods'  => array(
        'email' => 'Электронная почта',
        'azure' => 'Microsoft Azure',
    ),
    'host' => ':attribute не является допустимым хостом',
    'host_port' => ':attribute не соответствует формату HOST :PORT',
    'unique_table_msg' => ':attribute уже был принят в :table',
);