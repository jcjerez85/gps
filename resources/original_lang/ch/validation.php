<?php
return [
    'accepted' => 'El :attribute debe ser aceptado.',
    'active_url' => 'El :attribute no es una URL valida.',
    'after' => 'El :attribute debe ser una fecha posterior a :date.',
    'alpha' => 'El :attribute solo puede contener letras.',
    'alpha_dash' => 'El :attribute solo puede contener letras, numeros, y guiones.',
    'alpha_num' => 'El :attribute solo puede contener letras y numeros.',
    'array' => 'El :attribute debe ser un array.',
    'before' => 'El :attribute debe ser una fecha anterior a :date.',
    'between' => [
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'file' => 'El :attribute debe estar entre :min y :max kilobytes.',
        'string' => 'El :attribute debe estar entre :min y :max caracteres.',
        'array' => 'El :attribute debe tener entre :min y :max items.',
    ],
    'confirmed' => 'El :attribute confirmacion no coincide.',
    'date' => 'El :attribute no es una fecha valida.',
    'date_format' => 'El :attribute no coincide con el formato :format.',
    'different' => 'El :attribute y :other deben ser diferentes.',
    'digits' => 'El :attribute debe tener :digits digitos.',
    'digits_between' => 'El :attribute debe tener entre :min y :max digitos.',
    'email' => 'El :attribute debe ser una direccion email valida.',
    'exists' => 'El selected :attribute es invalido.',
    'image' => 'El :attribute debe ser una imagen.',
    'in' => 'El selected :attribute es invalido.',
    'integer' => 'El :attribute debe ser un entero.',
    'ip' => 'El :attribute debe ser una direccion IP valida.',
    'max' => [
        'numeric' => 'El :attribute no debe ser mayor a :max.',
        'file' => 'El :attribute no debe ser mayor a :max kilobytes.',
        'string' => 'El :attribute no debe ser mayor a :max caracteres.',
        'array' => 'El :attribute no debe tener mas de  :max items.',
    ],
    'mimes' => 'El :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'El :attribute debe tener al menos :min.',
        'file' => 'El :attribute debe tener al menos :min kilobytes.',
        'string' => 'El :attribute debe tener al menos :min caracteres.',
        'array' => 'El :attribute debe tener al menos :min items.',
    ],
    'not_in' => 'El selecccionado :attribute es invalido.',
    'numeric' => 'El :attribute debe ser un numero.',
    'regex' => 'El :attribute formato es invalido.',
    'required' => 'El :attribute campo es requerido.',
    'required_if' => 'El :attribute campo es requerido.',
    'required_with' => 'El :attribute campo es requerido cuando :values esta presente.',
    'required_with_all' => 'El :attribute campo es requerido cuando :values esta presente.',
    'required_without' => 'El :attribute campo es requerido cuando :values no esta presente.',
    'required_without_all' => 'El :attribute campo es requerido cuando ninguno de :values esta presente.',
    'same' => 'El :attribute y :other deben coincidir.',
    'size' => [
        'numeric' => 'El :attribute debe tener :size.',
        'file' => 'El :attribute debe tener :size kilobytes.',
        'string' => 'El :attribute debe tener :size caracteres.',
        'array' => 'El :attribute must contain :size items.',
    ],
    'unique' => 'El :attribute ya fue tomado.',
    'url' => 'El :attribute formato es invalido.',
    'array_max' => 'The :attribute max items :max.',
    'lesser_than' => 'El :attribute debe ser menor que :other',
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'Mensaje personalizado',
        ],
        'frontpage_logo' => [
            'dimensions' => 'La altura máxima del logotipo de la página principal es de 60 píxeles.',
        ],
        'favicon' => [
            'dimensions' => 'El favicon debe ser de 16 px por 16 px.',
        ],
    ],
    'attributes' => [
        'email' => 'Email',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar contraseña',
        'remember_me' => 'Recuerdame',
        'name' => 'Nombre',
        'imei' => 'IMEI',
        'imei_device' => 'identificador de IMEI o Dispositivo',
        'fuel_measurement_type' => 'Medida de combustible',
        'fuel_cost' => 'Costo de combustible',
        'icon_id' => 'Icono de dispositivo',
        'active' => 'Activo',
        'polygon_color' => 'Color de Fondo',
        'devices' => 'Dispositovos',
        'geofences' => 'Geocercas',
        'overspeed' => 'Exceso de velocidad',
        'fuel_consumption' => 'Consumo de combustible',
        'description' => 'Descripción',
        'map_icon_id' => 'Icono de marcador',
        'coordinates' => 'Punto en el mapa',
        'date_from' => 'Fecha desde',
        'date_to' => 'Fecha hasta',
        'code' => 'Codigo',
        'title' => 'Titulo',
        'note' => 'Contenido',
        'path' => 'Archivo',
        'period_name' => 'Nombre de periodo',
        'days' => 'Dias',
        'devices_limit' => 'Limite de dispositivos',
        'trial' => 'Pruebas',
        'price' => 'Precio',
        'message' => 'Mensaje',
        'tag' => 'Parámetro',
        'timezone_id' => 'Zona horaria',
        'unit_of_distance' => 'Unidad de distancia',
        'unit_of_capacity' => 'Unidad de capacidad',
        'user' => 'Usuario',
        'group_id' => 'Grupo',
        'permission_to_add_devices' => 'Agregar dispositivos',
        'unit_of_altitude' => 'Unidad de altitud',
        'sms_gateway_url' => 'SMS gateway URL',
        'mobile_phone' => 'Celular',
        'permission_to_use_sms_gateway' => 'SMS gateway',
        'loged_at' => 'Último acceso',
        'manager_id' => 'Gerente',
        'sim_number' => 'Número SIM',
        'device_model' => 'Modelo del dispositivo',
        'rfid' => 'RFID',
        'phone' => 'Teléfono',
        'device_id' => 'Dispositivo',
        'tag_value' => 'Valor tag',
        'device_port' => 'Puerto del dispositivo',
        'event' => 'Evento',
        'port' => 'Puerto',
        'device_protocol' => 'Protocolo de dispositivos',
        'protocol' => 'Protocolo',
        'sensor_name' => 'Nombre del sensor',
        'sensor_type' => 'Tipo de sensor',
        'sensor_template' => 'Plantilla del sensor',
        'tag_name' => 'Nombre de la etiqueta',
        'min_value' => 'Min. valor',
        'max_value' => 'Max. valor',
        'on_value' => 'En el valor',
        'off_value' => 'Valor fuera',
        'shown_value_by' => 'Mostrar valor',
        'full_tank_value' => 'Valor del parámetro',
        'formula' => 'Fórmula',
        'parameters' => 'Parámetros',
        'full_tank' => 'Tanque completo en litros/galones',
        'fuel_tank_name' => 'Nombre del tanque de combustible',
        'odometer_value' => 'Valor',
        'odometer_value_by' => 'Cuentakilómetros',
        'unit_of_measurement' => 'La unidad de medida',
        'plate_number' => 'Número de placa',
        'vin' => 'VIN',
        'registration_number' => 'Número de registro/de activos',
        'object_owner' => 'Propietario del Bateria/Gerente',
        'additional_notes' => 'Notas adicionales',
        'expiration_date' => 'Fecha de vencimiento',
        'days_to_remind' => 'Días para recordar antes de la expiración',
        'type' => 'Tipo',
        'format' => 'Formato',
        'show_addresses' => 'Mostrar direcciones',
        'stops' => 'Detiene',
        'speed_limit' => 'Límite de velocidad',
        'zones_instead' => 'Zonas en lugar de direcciones',
        'daily' => 'Diariamente',
        'weekly' => 'Semanal',
        'send_to_email' => 'Enviar al correo electrónico',
        'filter' => 'Filtro',
        'status' => 'Estado',
        'date' => 'Fecha',
        'geofence_name' => 'Nombre de Geocerca',
        'tail_color' => 'Color de la cola',
        'tail_length' => 'Longitud de la cola',
        'engine_hours' => 'Horas del motor',
        'detect_engine' => 'Detectar motor ON/OFF',
        'min_moving_speed' => 'Min . velocidad de movimiento en km/h',
        'min_fuel_fillings' => 'Min . diferencia de combustible para detectar rellenos de combustible',
        'min_fuel_thefts' => 'Min . diferencia de combustible para detectar robos de combustible',
        'expiration_by' => 'Vence en',
        'interval' => 'Intervalo',
        'last_service' => 'último servicio',
        'trigger_event_left' => 'Activar evento cuando falten',
        'current_odometer' => 'Odómetro actual',
        'current_engine_hours' => 'Horas del motor actuales',
        'renew_after_expiration' => 'Renovar después de la expiración',
        'sms_template_id' => 'Plantilla de SMS',
        'frequency' => 'Frecuencia',
        'unit' => 'Unidad',
        'noreply_email' => 'Ninguna dirección de correo electrónico de respuesta',
        'signature' => 'Firma',
        'use_smtp_server' => 'Usar servidor SMTP',
        'smtp_server_host' => 'Host del servidor SMTP',
        'smtp_server_port' => 'Puerto del servidor SMTP',
        'smtp_security' => 'Seguridad SMTP',
        'smtp_username' => 'Nombre de usuario SMTP',
        'smtp_password' => 'Contraseña SMTP',
        'from_name' => 'De nombre',
        'icons' => 'Iconos',
        'server_name' => 'Nombre del servidor',
        'available_maps' => 'Mapas disponibles',
        'default_language' => 'Idioma predeterminado',
        'default_timezone' => 'Por defecto la zona horaria',
        'default_unit_of_distance' => 'Unidad por defecto de la distancia',
        'default_unit_of_capacity' => 'Unidad por defecto de la capacidad',
        'default_unit_of_altitude' => 'Unidad por defecto de la altitud',
        'default_date_format' => 'Formato de fecha por defecto',
        'default_time_format' => 'Formato de hora predeterminado',
        'default_map' => 'Mapa predeterminado',
        'default_object_online_timeout' => 'Por defecto objeto de tiempo de espera en línea',
        'logo' => 'Logo',
        'login_page_logo' => 'Entrar en la página logo',
        'frontpage_logo' => 'Logo Frontpage',
        'favicon' => 'Favicon',
        'allow_users_registration' => 'Permite el registro de usuarios',
        'default_maps' => 'Mapas por defecto',
        'subscription_expiration_after_days' => 'Caducidad suscripción después de días',
        'gprs_template_id' => 'Plantilla GPRS',
        'calibrations' => 'Calibraciones',
        'ftp_server' => 'FTP servidor',
        'ftp_port' => 'FTP puerto',
        'ftp_username' => 'FTP nombre de usuario',
        'ftp_password' => 'FTP contraseña',
        'ftp_path' => 'FTP ruta',
        'period' => 'Periodo',
        'hour' => 'Hora',
        'color' => 'Color',
        'polyline' => 'Ruta',
        'request_method' => 'Solicitud método',
        'authentication' => 'Autenticación',
        'username' => 'Nombre de usuario',
        'encoding' => 'Codificación',
        'time_adjustment' => 'Ajuste de la hora',
        'parameter' => 'Parámetro',
        'export_type' => 'Tipo de exportación',
        'groups' => 'Grupos',
        'file' => 'Archivo',
        'enable_plans' => 'Habilitar planes',
        'payment_type' => 'Tipo de pago',
        'paypal_client_id' => 'Paypal ID de cliente',
        'paypal_secret' => 'Secreto paypal',
        'paypal_currency' => 'Moneda paypal',
        'paypal_payment_name' => 'Paypal nombre de pago',
        'objects' => 'Baterias',
        'duration_value' => 'Duración',
        'permissions' => 'Permisos',
        'plan' => 'Plan',
        'default_billing_plan' => 'Plan de facturación por defecto',
        'phone_number' => 'Número de teléfono',
        'action' => 'Acción',
        'time' => 'Hora',
        'order' => 'Orden',
        'geocoder_api' => 'Geocoder API',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'API key',
        'api_url' => 'API url',
        'map_center_latitude' => 'Latitud del centro del mapa',
        'map_center_longitude' => 'Longitud centro del mapa',
        'map_zoom_level' => 'Mapa nivel de zoom',
        'dst_type' => 'Tipo',
        'provider' => 'Proveedor',
        'week_start_day' => 'Día de inicio de la semana del calendario predeterminado',
        'ip' => 'IP',
        'gprs_templates_only' => 'Mostrar plantillas de comandos sólo GPRS',
        'select_all_objects' => 'Seleccionar todos los Baterias',
        'icon_type' => 'Tipo de Icono',
        'on_setflag_1' => 'Caracter inicial',
        'on_setflag_2' => 'Cantidad de caracteres',
        'on_setflag_3' => 'Valor del parámetro',
        'domain' => 'Dominio',
        'auth_id' => 'ID de Autenticación',
        'auth_token' => 'Token de Auth',
        'senders_phone' => 'Numero de telefono del remitente',
        'database_clear_status' => 'Limpieza automática del historial',
        'database_clear_days' => 'Días para guardar',
        'ignition_detection' => 'Detección de encendido por',
        'template_color' => 'Color de Plantilla',
        'background' => 'Fondo',
        'login_page_text_color' => 'Color de texto de la pagina de Inicio',
        'login_page_background_color' => 'Color de fondo de la pagina de inicio',
        'welcome_text' => 'Texto de bienvenida',
        'bottom_text' => 'Texto de abajo',
        'apple_store_link' => 'Enlace AppleStore',
        'google_play_link' => 'Enlace GooglePlay',
        'here_map_id' => 'HERE.com app ID',
        'here_map_code' => 'HERE.com app code',
        'login_page_panel_background_color' => 'Color de fondo de panel de pagina de inicio',
        'login_page_panel_transparency' => 'Transparencia de pagina de panel de inicio',
        'visible' => 'Visible',
        'extra' => 'Extra',
        'parameter_value' => 'Valor del parámetro',
        'sensor_group_id' => 'Grupo de sensores',
        'daylight_saving_time' => 'Horario de verano',
        'position' => 'Posición',
        'stop_duration_longer_than' => 'Detener la duración más de',
        'mapbox_access_token' => 'Token de acceso a MapBox',
        'flag' => 'Bandera',
        'shift_start' => 'Shift start',
        'shift_finish' => 'Shift finish',
        'shift_start_tolerance' => 'Shift start tolerancia',
        'shift_finish_tolerance' => 'Cambiar tolerancia final',
        'excessive_exit' => 'Excesiva salida',
        'smtp_authentication' => 'Autenticación SMTP',
        'skip_calibration' => 'Excluir cálculos fuera del rango de calibración',
        'bing_maps_key' => 'Clave de mapas de Bing',
        'stripe_public_key' => 'STRIPE clave pública',
        'stripe_secret_key' => 'STRIPE clave secreta',
        'stripe_currency' => 'STRIPE moneda',
        'priority' => 'Prioridad',
        'pickup_address' => 'Dirección de entrega',
        'delivery_address' => 'Dirección de entrega',
        'schedule' => 'Programar',
        'sound_notification' => 'Notificación de sonido',
        'push_notification' => 'Notifiación Push',
        'email_notification' => 'Notificación de correo electrónico',
        'sms_notification' => 'Notificación SMS',
        'webhook_notification' => 'Notificación webhook',
        'offline_duration_longer_than' => 'Duración fuera de línea más larga que',
        'sms_gateway_headers' => 'Cabeceras de puerta de enlace de SMS',
        'forward' => 'Adelante',
        'by_status' => 'Por estado',
        'icon_status_online' => 'Icono de estado en línea',
        'icon_status_offline' => 'Icono de estado sin conexión',
        'icon_status_ack' => 'Icono de estado de ack',
        'icon_status_engine' => 'Icono de estado del motor',
        'server_description' => 'Descripción del servidor',
        'bypass_invalid' => 'Permitir coordenadas inválidas',
        'installation_date' => 'Fecha de instalación',
        'sim_activation_date' => 'Fecha de activación de SIM',
        'sim_expiration_date' => 'Fecha de vencimiento de SIM',
        'activation_date' => 'Fecha de activacion',
        'secret_key' => 'Llave secreta',
        'currency' => 'Moneda',
        'client_id' => 'Identificación del cliente',
        'secret' => 'Secreto',
        'payment_name' => 'Nombre de pago',
        'merchant_id' => 'Identificación del comerciante',
        'public_key' => 'Llave pública',
        'private_key' => 'Llave privada',
        'braintree_plan_ids' => 'ID de plan de Braintree para planes de servidor',
        'braintree_plan_explanation' => 'Debe crear un plan de facturación recurrente en el panel de control de Braintree, seleccione la ID correspondiente al plan de facturación correspondiente en su servidor.',
        'braintree_merchant_explanation' => 'Debe crear una cuenta de comerciante en el panel de control de Braintree e ingresar la ID aquí.',
        'braintree_currency_match' => 'La moneda de la cuenta mercantil debe coincidir con la moneda del plan',
        'merchant_account_id' => 'Cuenta mercantil ID',
        'master_key' => 'Llave maestra',
        'token' => 'Simbólico',
        'paydunya_currency_warning' => 'La única moneda disponible es FCFA. Si lo configura, asegúrese de que sus otros pagos coincidan con la misma moneda. De lo contrario, los usuarios tendrán la oportunidad de comprar el mismo plan con diferentes precios.',
        'country' => 'País',
        'merchant_account' => 'Cuenta comercial',
        'origin_key' => 'Clave de origen',
        'test_config' => 'Configuración de prueba',
        'environment' => 'Ambiente',
        'three_letter_iso' => 'Código ISO de tres letras',
        'google_maps_key' => 'Clave API de Google maps',
        'maptiler_key' => 'Clave maptiler',
        'streetview_api' => 'Streetview API',
        'streetview_key' => 'Clave API de Streetview',
        'openmaptiles_url' => 'OpenMapTiles Url',
        'unit_cost' => 'Costo unitario',
        'supplier' => 'Proveedor',
        'buyer' => 'Comprador',
        'expense_type' => 'Tipo de gasto',
        'device_cameras_days' => 'Días para guardar las imágenes de la cámara del dispositivo',
        'api_app_id' => 'ID de la aplicación',
        'api_app_code' => 'Código de aplicación',
        'api_app_secret' => 'App secreta',
        'invoice_number' => 'Número de factura',
        'one_time_payment' => 'Pago único',
        'sharing_id' => 'Compartir',
        'idle_duration_longer_than' => 'Duración inactiva más larga que',
        'delete_after_expiration' => 'Eliminar después de vencimiento',
        'sharing_email' => 'Notificación por correo electrónico con enlace para compartir',
        'sharing_sms' => 'Notificación por SMS con enlace para compartir',
        'sms' => 'SMS',
        'template' => 'Modelo',
        'commands' => 'Comandos',
        'brand' => 'Fabricante del dispositivo',
        'model' => 'Modelo',
        'apn_name' => 'Nombre APN',
        'apn_username' => 'Nombre de usuario APN',
        'apn_password' => 'Contraseña APN',
        'ignition_duration_longer_than' => 'Duración de ignición más larga que',
        'tasks' => 'Tareas',
        'id' => 'CARNÉ DE IDENTIDAD',
        'columns' => 'Columnas',
        'called_at' => 'Llama a',
        'alert_type' => 'Tipo de alerta',
        'response' => 'Respuesta',
        'remarks' => 'Observaciones',
        'client' => 'Cliente',
        'event_type' => 'Tipo de evento',
        'data_type' => 'Tipo de datos',
        'slug' => 'Babosa',
        'required' => 'Necesario',
        'validation' => 'Validación',
        'text' => 'Texto',
        'datetime' => 'Fecha y hora',
        'boolean' => 'Booleano',
        'select' => 'Seleccione',
        'multiselect' => 'Selección múltiple',
        'options' => 'Opciones',
        'option' => 'Opción',
        'default' => 'Defecto',
        'msisdn' => 'MSISDN',
        'notes' => 'Notas',
        'skip_empty' => 'Omitir valor vacío',
        'distance_limit' => 'Límite de distancia',
        'distance_tolerance' => 'Tolerancia de distancia',
        'pois' => 'PDI',
        'device_type_id' => 'Tipo de dispositivo',
        'custom_fields' => 'Campos Personalizados',
        'device_name' => 'Nombre del dispositivo',
        'auto_hide_notification' => 'Ocultar automáticamente la ventana emergente',
        'continuous_duration' => 'Duración continua',
        'captcha_provider' => 'Proveedor CAPTCHA',
        'google_recaptcha' => 'Google reCAPTCHA',
        'recaptcha_site_key' => 'Clave del sitio reCAPTCHA',
        'recaptcha_secret_key' => 'ReCAPTCHA clave secreta',
        'g-recaptcha-response' => 'ReCAPTCHA',
        'here_api_key' => 'Clave API de HERE.com',
        'time_duration' => 'Duración de tiempo',
        'offset' => 'Compensar',
        'geofence_device' => 'Dispositivo',
        'webhook_key' => 'Clave de webhook',
        'skip_blank_results' => 'Omitir resultados en blanco',
        'account_sid' => 'SID de la cuenta',
        'speed_limit_tolerance' => 'Tolerancia de límite de velocidad',
        'started_at' => 'Hora de inicio',
        'ended_at' => 'Hora de finalización',
        'region' => 'Región',
        'adapted' => 'Adaptado',
        'silent_notification' => 'Ignorar notificaciones si se repiten en minutos',
        'tomtom_key' => 'Tecla tomtom',
        'authorized' => 'Autorizado',
        'email_verification' => 'Verificacion de email',
        'project_id' => 'Projecto ID',
        'project_psw' => 'Contraseña del proyecto',
        'verify_id' => 'Verificar identificación',
        'app_tracker_login' => 'Inicio de sesión de la aplicación de seguimiento habilitado',
        'merchant_code' => 'Código de comerciante',
        'count' => 'Contar',
        'detect_distance' => 'Detección de distancia por',
        'detect_speed' => 'Detección de velocidad por',
        'routes' => 'Rutas',
        'battery_threshold' => 'Umbral de batería',
        'state' => 'Estado',
        'duration' => 'Duración',
        'statuses' => 'Estados',
        'first_name' => 'Primer nombre',
        'last_name' => 'Apellido',
        'personal_code' => 'Código personal',
        'birth_date' => 'Fecha de nacimiento',
        'company_id' => 'Compañía',
        'registration_code' => 'Código de registro',
        'vat_number' => 'Número de valor agregado',
        'address' => 'Dirección',
        'comment' => 'Comentario',
        'duration_format' => 'Formato de duración',
        'default_duration_format' => 'Formato de duración predeterminado',
        'login_token' => 'Simbólico',
        'monthly' => 'Mensual',
        'amount' => 'Cantidad',
        'bad_sms_gateway_url' => 'Configuración o URL de puerta de enlace de SMS incorrecta',
        'rates' => 'Tarifas',
        'fields' => 'Campos',
        'tenant_id' => 'ID de inquilino',
        'client_secret' => 'Secreto del cliente',
        'default_login_methods' => 'Métodos de inicio de sesión predeterminados',
        'forwards' => 'Hacia adelante',
        'detection_speed' => 'Velocidad de detección',
        'detach_on_no_position_data' => 'Separar sin datos de posición',
        'extra_expiration_time' => 'Tiempo de caducidad adicional',
        'fuel_detect_sec_after_stop' => 'Detectar el cambio de combustible después de la parada',
        'login_periods' => 'Períodos de inicio de sesión',
    ],
    'same_protocol' => 'Los dispositivos deben ser del mismo protocolo.',
    'contains' => 'El :attribute debe contener :value .',
    'ip_port' => 'El :attribute no coincide con el formato IP:PORT',
    'required_unless' => 'El campo :attribute es obligatorio.',
    'translation_file' => 'El archivo de traducción no existe',
    'placeholder' => 'Falta el atributo &quot; :placeholder &quot;',
    'image_valid' => 'El :attribute debe ser una imagen.',
    'missing_configuration_value' => 'Falta :attribute valor de configuración del :attribute .',
    'sms_gateway_error' => 'El mensaje no se puede enviar. Error de puerta de enlace SMS.',
    'cant_configure_device' => 'No se pudo configurar el dispositivo',
    'field_does_not_exist' => 'Campo :attribute no existe',
    'unsupported_rules' => 'Reglas no compatibles:',
    'unsupported_parameterized_rules' => 'Reglas parametrizadas no compatibles:',
    'cant_update_custom_field' => 'No se puede actualizar el campo &quot; :field &quot; porque hay registros existentes que utilizan este campo personalizado',
    'strong_password' => 'Contraseña segura',
    'uppercase_character' => 'Se requiere carácter en mayúsculas',
    'lowercase_character' => 'Se requiere un carácter en minúscula',
    'digit_character' => 'Se requiere carácter de dígito',
    'wrong_captcha' => 'Captcha incorrecto',
    'dimensions' => 'El :attribute tiene dimensiones de imagen no válidas.',
    'mimetypes' => 'El :attribute debe ser un archivo de tipo :values .',
    'in_array' => 'El :attribute no existe en :other .',
    'uploaded' => 'El :attribute no se pudo cargar. Es posible que el servidor no acepte tal tamaño.',
    'login_methods' => [
        'email' => 'Correo electrónico',
        'azure' => 'Microsoft azure',
    ],
    'host' => 'El :attribute no es un host válido',
    'host_port' => 'El :attribute no coincide con el formato HOST :PORT',
    'unique_table_msg' => 'El :attribute ya ha sido tomado en :table',
];
