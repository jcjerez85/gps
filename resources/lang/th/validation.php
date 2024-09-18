<?php

return array(

    'accepted' => ':attribute จะต้องเป็นไปตามที่กำหนด',
    'active_url' => ':attribute ไม่ได้เป็น URL ที่ถูกต้อง.',
    'after' => ':attribute ต้องเป็นวันที่หลังจาก :date.',
    'alpha' => ':attribute อาจจะมีเพียงตัวอักษร.',
    'alpha_dash' => ':attribute อาจจะประกอบด้วยตัวอักษร ตัวเลข และเครื่องหมายขีดกลาง',
    'alpha_num' => ':attribute อาจจะมีเพียงตัวอักษรและตัวเลข.',
    'array' => ':attribute จะต้องเป็น array',
    'before' => ':attribute ต้องเป็นวันที่ก่อน :date.',
    'between'  => array(
        'numeric' => ':attribute จะต้องอยู่ระหว่าง :min และ :max.',
        'file' => ':attribute จะต้องอยู่ระหว่างจำนวน :min และ :max กิโลไบต์.',
        'string' => ':attribute จะต้องอยู่ระหว่างจำนวน :min และ :max ตัวอักษร',
        'array' => ':attribute จะต้องอยู่ระหว่างจำนวน :min และ :max ของรายการ',
    ),
    'confirmed' => ':attribute ข้อมูลยืนยันไม่ตรงกัน',
    'date' => ':attribute ไม่ได้เป็นวันที่ถูกต้อง.',
    'date_format' => ':attribute ไม่ตรงกับรูปแบบ :format.',
    'different' => ':attribute และ :other จะต้องมีความแตกต่างกัน.',
    'digits' => ':attribute จะต้องเป็น :digits ตัวเลข.',
    'digits_between' => ':attribute จะต้องเป็น ระหว่าง :min และ :max ตัวเลข.',
    'email' => ':attribute จะต้องเป็น ที่อยู่อีเมลที่ถูกต้อง.',
    'exists' => ':attribute ที่เลือกไม่ถูกต้อง.',
    'image' => ':attribute จะต้องเป็น ภาพ.',
    'in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'integer' => ':attribute จะต้องเป็น จำนวนเต็ม.',
    'ip' => ':attribute จะต้องเป็น ที่อยู่ IP ที่ถูกต้อง.',
    'max'  => array(
        'numeric' => ':attribute ไม่อาจเกินกว่า :max.',
        'file' => ':attribute ไม่อาจเกินกว่า :max กิโลไบต์',
        'string' => ':attribute ไม่อาจเกินกว่า :max ตัวอักษร',
        'array' => ':attribute ไม่อาจมากกว่า :max รายการ',
    ),
    'mimes' => ':attribute จะต้องเป็นไฟล์ชนิด: :values.',
    'min'  => array(
        'numeric' => ':attribute ต้องมีอย่างน้อย :min.',
        'file' => ':attribute ต้องมีอย่างน้อย :min กิโลไบต์.',
        'string' => ':attribute ต้องมีอย่างน้อย :min ตัวอักษร.',
        'array' => ':attribute ต้องมีอย่างน้อย :min รายการ.',
    ),
    'not_in' => ':attribute ที่เลือกไม่ถูกต้อง.',
    'numeric' => ':attribute ต้องเป็นตัวเลข.',
    'regex' => 'รูปแบบ :attribute ไม่ถูกต้อง.',
    'required' => ':attribute ในส่วนนี้ต้องมี',
    'required_if' => ':attribute ในส่วนนี้ต้องมี',
    'required_with' => ':attribute ในส่วนนี้ต้องมี ในกรณีมี :values',
    'required_with_all' => ':attribute ในส่วนนี้ต้องมี ในกรณีมี :values',
    'required_without' => ':attribute ในส่วนนี้ต้องมี ในกรณีไม่มี :values',
    'required_without_all' => ':attribute ในส่วนนี้ต้องมี ในกรณีไม่มี :values ใดๆเลย',
    'same' => ':attribute และ :other จะต้องตรงกัน',
    'size'  => array(
        'numeric' => ':attribute จะต้องเป็น :size.',
        'file' => ':attribute จะต้องเป็น :size กิโลไบต์.',
        'string' => ':attribute จะต้องเป็น :size ตัวอักษร.',
        'array' => ':attribute จะต้องประกอบด้วย :size รายการ',
    ),
    'unique' => ':attribute มีการใช้แล้ว',
    'url' => 'รูปแบบ :attribute ไม่ถูกต้อง.',
    'array_max' => ':attribute จำนวนรายการสูงสุด :max',
    'lesser_than' => ':attribute จะต้องน้อยกว่า :other',
    'custom'  => array(
        'attribute-name'  => array(
            'rule-name' => 'custom-message',
        ),
        'frontpage_logo'  => array(
            'dimensions' => 'โลโก้ Frontpage สูงไม่เกิน 60px',
        ),
        'favicon'  => array(
            'dimensions' => 'Favicon ต้องมีขนาด 16px x 16px',
        ),
    ),
    'attributes'  => array(
        'email' => 'อีเมล์',
        'password' => 'รหัสผ่าน',
        'password_confirmation' => 'ยืนยันรหัสผ่าน',
        'remember_me' => 'จำข้อมูลไว้',
        'name' => 'ชื่อ',
        'imei' => 'IMEI',
        'imei_device' => 'IMEI หรือตัวระบุอุปกรณ์',
        'fuel_measurement_type' => 'วัดเชื้อเพลิง',
        'fuel_cost' => 'ค่าใช้จ่ายเชื้อเพลิง',
        'icon_id' => 'สัญลักษณ์ของอุปกรณ์',
        'active' => 'ทำงาน',
        'polygon_color' => 'สีพื้นหลัง',
        'devices' => 'อุปกรณ์',
        'geofences' => 'รั้วภูมิศาสตร์',
        'overspeed' => 'ความเร็วเกินกำหนด',
        'fuel_consumption' => 'อัตราการใช้เชื้อเพลิง',
        'description' => 'ลักษณะ',
        'map_icon_id' => 'เครื่องหมาย',
        'coordinates' => 'ตำแหน่งบนแผนที่',
        'date_from' => 'จากวันที่',
        'date_to' => 'ไปถึงวันที่',
        'code' => 'รหัส',
        'title' => 'ชื่อเรื่อง',
        'note' => 'เนื้อหา',
        'path' => 'ไฟล์',
        'period_name' => 'ชื่อช่วงเวลา',
        'days' => 'วัน',
        'devices_limit' => 'จำกัดจำนวนอุปกรณ์',
        'trial' => 'การทดลอง',
        'price' => 'ราคา',
        'message' => 'ข้อความ',
        'tag' => 'พารามิเตอร์',
        'timezone_id' => 'เขตเวลา',
        'unit_of_distance' => 'หน่วยของระยะทาง',
        'unit_of_capacity' => 'หน่วยความจุ',
        'user' => 'ผู้ใช้งาน',
        'group_id' => 'กลุ่ม',
        'permission_to_add_devices' => 'เพิ่มอุปกรณ์',
        'unit_of_altitude' => 'หน่วยของความสูง',
        'sms_gateway_url' => 'URL ของ SMS เกตเวย์',
        'mobile_phone' => 'โทรศัพท์มือถือ',
        'permission_to_use_sms_gateway' => 'เกตเวย์ SMS',
        'loged_at' => 'เข้าสู่ระบบล่าสุด',
        'manager_id' => 'ผู้จัดการ',
        'sim_number' => 'หมายเลขซิม',
        'device_model' => 'รุ่นอุปกรณ์',
        'rfid' => 'RFID',
        'phone' => 'โทรศัพท์',
        'device_id' => 'อุปกรณ์',
        'tag_value' => 'ค่าพารามิเตอร์',
        'device_port' => 'พอร์ตอุปกรณ์',
        'event' => 'เหตุการณ์',
        'port' => 'พอร์ต',
        'device_protocol' => 'โปรโตคอลของอุปกรณ์',
        'protocol' => 'โปรโตคอล',
        'sensor_name' => 'ชื่อเซนเซอร์',
        'sensor_type' => 'ประเภทเซนเซอร์',
        'sensor_template' => 'รูปแบบเซนเซอร์',
        'tag_name' => 'ชื่อพารามิเตอร์',
        'min_value' => 'ค่าต่ำสุด',
        'max_value' => 'ค่าสูงสุด',
        'on_value' => 'ค่า เปิด',
        'off_value' => 'ค่า ปิด',
        'shown_value_by' => 'แสดงค่าโดย',
        'full_tank_value' => 'ค่าพารามิเตอร์',
        'formula' => 'สูตร',
        'parameters' => 'พารามิเตอร์',
        'full_tank' => 'เต็มถัง หน่วย ลิตร/แกลลอน',
        'fuel_tank_name' => 'ชื่อของถังเชื้อเพลิง',
        'odometer_value' => 'ค่า',
        'odometer_value_by' => 'วัดระยะทาง',
        'unit_of_measurement' => 'หน่วยของการวัด',
        'plate_number' => 'หมายเลขป้ายทะเบียน',
        'vin' => 'หมายเลขตัวถังรถ',
        'registration_number' => 'เลขทะเบียน ทรัพย์สิน',
        'object_owner' => 'เจ้าของ/ผู้จัดการ',
        'additional_notes' => 'บันทึกเพิ่มเติม',
        'expiration_date' => 'วันหมดอายุ',
        'days_to_remind' => 'จำนวนวันแจ้งเตือนก่อนหมดอายุ',
        'type' => 'ชนิด',
        'format' => 'รูปแบบ',
        'show_addresses' => 'แสดง ที่อยู่',
        'stops' => 'หยุด',
        'speed_limit' => 'จำกัด ความเร็ว',
        'zones_instead' => 'โซนพื้นที่ แทนที่อยู่',
        'daily' => 'รายวัน',
        'weekly' => 'รายสัปดาห์',
        'send_to_email' => 'ส่งไปที่ อีเมล',
        'filter' => 'ตัวกรอง',
        'status' => 'สถานะ',
        'date' => 'วันที่',
        'geofence_name' => 'ชื่อของ รั้วภูมิศาสตร์',
        'tail_color' => 'สีของเส้นทางที่เพิ่งผ่านมา',
        'tail_length' => 'ความยาว ของเส้นทางที่เพิ่งผ่านมา',
        'engine_hours' => 'จำนวนชั่วโมงที่เครื่องยนต์ทำงาน',
        'detect_engine' => 'ตรวจพบการ เปิด/ปิด เครื่องยนต์ โดย',
        'min_moving_speed' => 'ความเร็วขั้นต่ำในการเคลื่อนที่ กิโลเมตร/ชั่วโมง',
        'min_fuel_fillings' => 'ระดับการเปลี่ยนแปลงขั้นต่ำเพื่อตรวจสอบการเติมเชื้อเพลิง',
        'min_fuel_thefts' => 'ระดับการเปลี่ยนแปลงขั้นต่ำเพื่อตรวจสอบการขโมยเชื้อเพลิง',
        'expiration_by' => 'หมดอายุ ภายใน',
        'interval' => 'ช่วงเวลา',
        'last_service' => 'บริการครั้งล่าสุด',
        'trigger_event_left' => 'เหตุการณ์เกิดขึ้น เมื่อเหลือ',
        'current_odometer' => 'วัดระยะทางปัจจุบัน',
        'current_engine_hours' => 'จำนวนชั่วโมงทำงานของเครื่องยนต์',
        'renew_after_expiration' => 'ต่ออายุ หลังจากวันหมดอายุ',
        'sms_template_id' => 'รูปแบบ SMS',
        'frequency' => 'ความถี่',
        'unit' => 'หน่วย',
        'noreply_email' => 'ไม่มีที่อยู่อีเมลตอบกลับ',
        'signature' => 'ลายเซ็น',
        'use_smtp_server' => 'ใช้ SMTP เซิร์ฟเวอร์',
        'smtp_server_host' => 'SMTP โฮสต์ เซิร์ฟเวอร์',
        'smtp_server_port' => 'SMTP พอร์ต เซิร์ฟเวอร์',
        'smtp_security' => 'การรักษาความปลอดภัย SMTP',
        'smtp_username' => 'ชื่อผู้ใช้ SMTP',
        'smtp_password' => 'รหัสผ่าน SMTP',
        'from_name' => 'จากชื่อ',
        'icons' => 'สัญลักษณ์',
        'server_name' => 'ชื่อเซิร์ฟเวอร์',
        'available_maps' => 'แผนที่ที่ใช้งานได้',
        'default_language' => 'ภาษา ที่เป็นค่าเริ่มต้น',
        'default_timezone' => 'เขตเวลา ที่เป็นค่าเริ่มต้น',
        'default_unit_of_distance' => 'หน่วยระยะทาง ที่เป็นค่าเริ่มต้น',
        'default_unit_of_capacity' => 'หน่วยความจุ ที่เป็นค่าเริ่มต้น',
        'default_unit_of_altitude' => 'หน่วยความสูง ที่เป็นค่าเริ่มต้น',
        'default_date_format' => 'รูปแบบวันที่ ที่เป็นค่าเริ่มต้น',
        'default_time_format' => 'รูปแบบเวลา ที่เป็นค่าเริ่มต้น',
        'default_map' => 'แผนที่ ที่เป็นค่าเริ่มต้น',
        'default_object_online_timeout' => 'การสิ้นสุดเวลาออนไลน์ของวัตถุ ที่เป็นค่าเริ่มต้น',
        'logo' => 'โลโก้',
        'login_page_logo' => 'โลโก้ ของหน้าเข้าสู่ระบบ',
        'frontpage_logo' => 'โลโก้ หน้าแรก',
        'favicon' => 'Favicon',
        'allow_users_registration' => 'อนุญาตการลงทะเบียนผู้ใช้',
        'default_maps' => 'แผนที่ ที่เป็นค่าเริ่มต้น',
        'subscription_expiration_after_days' => 'การหมดอายุ ในภายหลัง',
        'gprs_template_id' => 'รูปแบบ GPRS',
        'calibrations' => 'การสอบเทียบค่า',
        'ftp_server' => 'FTP เซิร์ฟเวอร์',
        'ftp_port' => 'FTP พอร์ต',
        'ftp_username' => 'ชื่อผู้ใช้ FTP',
        'ftp_password' => 'รหัสผ่าน FTP',
        'ftp_path' => 'เส้นทาง FTP',
        'period' => 'ระยะเวลา',
        'hour' => 'ชั่วโมง',
        'color' => 'สี',
        'polyline' => 'เส้นทาง',
        'request_method' => 'Request method',
        'authentication' => 'รับรองความถูกต้อง',
        'username' => 'ชื่อผู้ใช้',
        'encoding' => 'การเข้ารหัส',
        'time_adjustment' => 'ปรับ เวลา',
        'parameter' => 'พารามิเตอร์',
        'export_type' => 'ประเภท การนำออก',
        'groups' => 'กลุ่ม',
        'file' => 'ไฟล์',
        'extra' => 'พิเศษ',
        'parameter_value' => 'ค่าพารามิเตอร์',
        'enable_plans' => 'เปิดใช้ แพคเกจ',
        'payment_type' => 'ประเภทการชำระเงิน',
        'paypal_client_id' => 'รหัส Paypal ลูกค้า',
        'paypal_secret' => 'ความลับ Paypal',
        'paypal_currency' => 'สกุลเงิน Paypal',
        'paypal_payment_name' => 'ชื่อการชำระเงิน PayPal',
        'objects' => 'วัตถุที่ตาม',
        'duration_value' => 'ระยะเวลา',
        'permissions' => 'สิทธิ์',
        'plan' => 'แพคเกจ',
        'default_billing_plan' => 'แพคเกจที่เป็นค่าเริ่มต้น',
        'sensor_group_id' => 'กลุ่มเซนเซอร์',
        'daylight_saving_time' => 'ปรับเวลาตามฤดูกาล',
        'phone_number' => 'หมายเลขโทรศัพท์',
        'action' => 'การกระทำ',
        'time' => 'เวลา',
        'order' => 'คำสั่ง',
        'geocoder_api' => 'Geocoder API',
        'geocoder_cache' => 'Geocoder cache',
        'geocoder_cache_days' => 'Geocoder cache days',
        'geocoder_cache_delete' => 'Delete geocoder cache',
        'api_key' => 'API key',
        'api_url' => 'API url',
        'map_center_latitude' => 'ละติจูดศูนย์กลางแผนที่',
        'map_center_longitude' => 'ลองจิจูดศูนย์กลางแผนที่',
        'map_zoom_level' => 'ระดับการขยายแผนที่',
        'dst_type' => 'ชนิด',
        'provider' => 'ผู้ให้บริการ',
        'week_start_day' => 'วันเริ่มต้นสัปดาห์ที่เป็นค่าเริ่มต้น',
        'ip' => 'IP',
        'gprs_templates_only' => 'แสดงคำสั่งที่เป็นรูปแบบ GPRS เท่านั้น',
        'select_all_objects' => 'เลือกวัตถ​​ุทั้งหมด',
        'icon_type' => 'ประเภทสัญลักษณ์',
        'on_setflag_1' => 'ตัวอักษรเริ่มต้น',
        'on_setflag_2' => 'จำนวนตัวอักษร',
        'on_setflag_3' => 'ค่าของพารามิเตอร์',
        'domain' => 'Domain',
        'auth_id' => 'Auth ID',
        'auth_token' => 'Auth token',
        'senders_phone' => 'หมายเลขโทรศัพท์ของผู้ส่ง',
        'database_clear_status' => 'ล้างข้อมูลย้อนหลังอัตโนมัติ',
        'database_clear_days' => 'จำนวนวันที่ให้เก็บไว้',
        'ignition_detection' => 'ตรวจสอบการสตาร์ทเครื่องยนต์โดย',
        'template_color' => 'รูปแบบสี',
        'background' => 'พื้นหลัง',
        'login_page_text_color' => 'สีข้อความของหน้าเข้าสู่ระบบ',
        'login_page_background_color' => 'สีพื้นหลังของหน้าเข้าสู่ระบบ',
        'welcome_text' => 'ข้อความต้อนรับ',
        'bottom_text' => 'ข้อความด้านล่าง',
        'apple_store_link' => 'ลิงก์ AppleStore',
        'google_play_link' => 'ลิงก์ GooglePlay',
        'here_map_id' => 'ID แอป HERE.com',
        'here_map_code' => 'รหัสแอป HERE.com',
        'login_page_panel_background_color' => 'สีพื้นหลังของแผงควบคุมหน้าเข้าสู่ระบบ',
        'login_page_panel_transparency' => 'ความโปร่งใสของหน้าแผงควบคุม',
        'visible' => 'มองเห็นได้',
        'position' => 'ตำแหน่ง',
        'stop_duration_longer_than' => 'ระยะเวลาหยุดนานกว่า',
        'mapbox_access_token' => 'MapBox access token',
        'flag' => 'ธง',
        'shift_start' => 'เริ่มต้นกะ',
        'shift_finish' => 'สิ้นสุดกะ',
        'shift_start_tolerance' => 'ความคลาดเคลื่อนของการเริ่มต้นกะ',
        'shift_finish_tolerance' => 'ความคลาดเคลื่อนของการสิ้นสุดกะ',
        'excessive_exit' => 'Excessive exit',
        'smtp_authentication' => 'การตรวจสอบสิทธิ์ SMTP',
        'skip_calibration' => 'Exclude calculations out of the calibration range',
        'bing_maps_key' => 'คีย์แผนที่ Bing',
        'stripe_public_key' => 'คีย์สาธารณะ STRIPE',
        'stripe_secret_key' => 'คีย์ลับ STRIPE',
        'stripe_currency' => 'สกุล STRIPE',
        'priority' => 'ลำดับความสำคัญ',
        'pickup_address' => 'ที่อยู่รถกระบะ',
        'delivery_address' => 'ที่อยู่สำหรับการจัดส่ง',
        'schedule' => 'ตารางเวลา',
        'sound_notification' => 'การแจ้งเตือนด้วยเสียง',
        'push_notification' => 'การแจ้งเตือนด้วยข้อความ',
        'email_notification' => 'การแจ้งเตือนทางอีเมล',
        'sms_notification' => 'การแจ้งเตือนทาง SMS',
        'webhook_notification' => 'การแจ้งเตือน Webhook',
        'offline_duration_longer_than' => 'ระยะเวลาออฟไลน์นานกว่า',
        'sms_gateway_headers' => 'ส่วนหัวข้อของ SMS เกตเวย์',
        'forward' => 'Forward',
        'by_status' => 'ตามสถานะ',
        'icon_status_online' => 'สัญลักษณ์สถานะออนไลน์',
        'icon_status_offline' => 'สัญลักษณ์สถานะออฟไลน์',
        'icon_status_ack' => 'สัญลักษณ์สถานะ Ack',
        'icon_status_engine' => 'สัญลักษณ์สถานะเครื่องยนต์',
        'server_description' => 'คำอธิบายเซิร์ฟเวอร์',
        'bypass_invalid' => 'Allow invalid coordinates',
        'installation_date' => 'วันที่ติดตั้ง',
        'sim_activation_date' => 'วันที่เปิดใช้งาน SIM',
        'sim_expiration_date' => 'วันหมดอายุของ SIM',
        'activation_date' => 'วันที่เปิดใช้งาน',
        'secret_key' => 'รหัสลับ',
        'currency' => 'สกุลเงิน',
        'client_id' => 'รหัสลูกค้า',
        'secret' => 'ลับ',
        'payment_name' => 'ชื่อการชำระเงิน',
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
        'country' => 'ประเทศ',
        'merchant_account' => 'บัญชีการค้า',
        'origin_key' => 'Origin key',
        'test_config' => 'ทดสอบการกำหนดค่า',
        'environment' => 'สภาพแวดล้อม',
        'three_letter_iso' => 'Three-letter ISO currency code',
        'google_maps_key' => 'รหัส Google maps API',
        'maptiler_key' => 'MapTiler key',
        'streetview_api' => 'API Streetview',
        'streetview_key' => 'Streetview API key',
        'openmaptiles_url' => 'OpenMapTiles Url',
        'unit_cost' => 'ต้นทุนต่อหน่วย',
        'supplier' => 'ผู้จัดหาอุปกรณ์',
        'buyer' => 'ผู้ซื้อ',
        'expense_type' => 'ประเภทค่าใช้จ่าย',
        'device_cameras_days' => 'จำนวนวันในการเก็บภาพจากกล้องของอุปกรณ์',
        'api_app_id' => 'App ID',
        'api_app_code' => 'App Code',
        'api_app_secret' => 'App Secret',
        'invoice_number' => 'เลขใบสั่งของ',
        'one_time_payment' => 'จ่ายครั้งเดียว',
        'sharing_id' => 'Sharing',
        'idle_duration_longer_than' => 'ระยะเวลารอบเดินเบา นานกว่า',
        'delete_after_expiration' => 'ลบหลังจากหมดอายุ',
        'sharing_email' => 'การแจ้งเตือนทางอีเมลพร้อมลิงก์ sharing',
        'sharing_sms' => 'การแจ้งเตือนทาง SMS พร้อมลิงก์ sharing',
        'sms' => 'SMS',
        'template' => 'รูปแบบ',
        'commands' => 'คำสั่ง',
        'brand' => 'ผู้ผลิตอุปกรณ์',
        'model' => 'แบบ',
        'apn_name' => 'ชื่อ APN',
        'apn_username' => 'ชื่อผู้ใช้ APN',
        'apn_password' => 'รหัสผ่าน APN',
        'ignition_duration_longer_than' => 'ระยะเวลาการจุดระเบิดเครื่องยนต์นานกว่า',
        'tasks' => 'งาน',
        'id' => 'ID',
        'columns' => 'ช่อง  คอลัมน์',
        'called_at' => 'โทรมาที่',
        'alert_type' => 'ประเภทการแจ้งเตือน',
        'response' => 'การตอบสนอง',
        'remarks' => 'หมายเหตุ',
        'client' => 'ลูกค้า',
        'event_type' => 'ประเภทเหตุการณ์',
        'data_type' => 'ประเภทข้อมูล',
        'slug' => 'Slug',
        'required' => 'ต้องมี',
        'validation' => 'การตรวจสอบ',
        'text' => 'ข้อความ',
        'datetime' => 'วันเวลา',
        'boolean' => 'Boolean',
        'select' => 'เลือก',
        'multiselect' => 'เลือกหลายอย่าง',
        'options' => 'ตัวเลือก',
        'option' => 'ตัวเลือก',
        'default' => 'ค่าเริ่มต้น',
        'msisdn' => 'MSISDN',
        'notes' => 'หมายเหตุ',
        'skip_empty' => 'ข้ามค่าว่าง',
        'distance_limit' => 'การจำกัด ระยะทาง',
        'distance_tolerance' => 'ความทนทานต่อระยะทาง',
        'pois' => 'จุดที่น่าสนใจ',
        'device_type_id' => 'ประเภทอุปกรณ์',
        'custom_fields' => 'ฟิลด์ที่กำหนดเอง',
        'device_name' => 'ชื่ออุปกรณ์',
        'auto_hide_notification' => 'ซ่อนป๊อปอัปอัตโนมัติ',
        'continuous_duration' => 'ระยะเวลาต่อเนื่อง',
        'captcha_provider' => 'ผู้ให้บริการ CAPTCHA',
        'google_recaptcha' => 'Google reCAPTCHA',
        'recaptcha_site_key' => 'คีย์ไซต์ reCAPTCHA',
        'recaptcha_secret_key' => 'รหัสลับ reCAPTCHA',
        'g-recaptcha-response' => 'ReCAPTCHA',
        'here_api_key' => 'รหัส HERE.com API',
        'time_duration' => 'ระยะเวลา',
        'offset' => 'ออฟเซ็ต',
        'geofence_device' => 'อุปกรณ์',
        'webhook_key' => 'คีย์เว็บฮุค',
        'skip_blank_results' => 'ข้ามผลลัพธ์ที่ว่างเปล่า',
        'account_sid' => 'บัญชี SID',
        'speed_limit_tolerance' => 'ความอดทนจำกัดความเร็ว',
        'started_at' => 'เวลาเริ่มต้น',
        'ended_at' => 'เวลาสิ้นสุด',
        'region' => 'ภูมิภาค',
        'adapted' => 'ดัดแปลง',
        'silent_notification' => 'ละเว้นการแจ้งเตือนหากทำซ้ำในไม่กี่นาที',
        'tomtom_key' => 'TomTom คีย์',
        'authorized' => 'ได้รับอนุญาต',
        'email_verification' => 'การยืนยันอีเมล',
        'project_id' => 'รหัสโครงการ',
        'project_psw' => 'รหัสผ่านโครงการ',
        'verify_id' => 'ยืนยันรหัส',
        'app_tracker_login' => 'เปิดใช้งานการเข้าสู่ระบบแอปติดตาม',
        'merchant_code' => 'รหัสร้านค้า',
        'count' => 'นับ',
        'detect_distance' => 'การตรวจจับระยะทางโดย',
        'detect_speed' => 'ตรวจจับความเร็วโดย',
        'routes' => 'เส้นทาง',
        'battery_threshold' => 'เกณฑ์แบตเตอรี่',
        'state' => 'สถานะ',
        'duration' => 'ระยะเวลา',
        'statuses' => 'สถานะ',
        'first_name' => 'ชื่อจริง',
        'last_name' => 'นามสกุล',
        'personal_code' => 'รหัสส่วนตัว',
        'birth_date' => 'วันที่เกิด',
        'company_id' => 'บริษัท',
        'registration_code' => 'รหัสลงทะเบียน',
        'vat_number' => 'หมายเลขภาษี',
        'address' => 'ที่อยู่',
        'comment' => 'ความคิดเห็น',
        'duration_format' => 'รูปแบบระยะเวลา',
        'default_duration_format' => 'รูปแบบระยะเวลาเริ่มต้น',
        'login_token' => 'โทเค็น',
        'monthly' => 'รายเดือน',
        'amount' => 'จำนวน',
        'bad_sms_gateway_url' => 'URL เกตเวย์หรือการกำหนดค่า SMS ไม่ถูกต้อง',
        'rates' => 'ราคา',
        'fields' => 'เขตข้อมูล',
        'tenant_id' => 'รหัสผู้เช่า',
        'client_secret' => 'ความลับของลูกค้า',
        'default_login_methods' => 'วิธีการเข้าสู่ระบบเริ่มต้น',
        'forwards' => 'ไปข้างหน้า',
        'detection_speed' => 'ความเร็วในการตรวจจับ',
        'detach_on_no_position_data' => 'แยกออกโดยไม่มีข้อมูลตำแหน่ง',
        'extra_expiration_time' => 'เวลาหมดอายุเพิ่มเติม',
        'fuel_detect_sec_after_stop' => 'ตรวจจับการเปลี่ยนเชื้อเพลิงหลังจากหยุด',
        'login_periods' => 'ระยะเวลาเข้าสู่ระบบ',
    ),
    'same_protocol' => 'อุปกรณ์ต้องเป็น protocol แบบเดียวกัน',
    'contains' => ':attribute ต้องมี :value',
    'ip_port' => ':attribute ไม่ตรงกับรูปแบบ IP:PORT',
    'required_unless' => 'ต้องมีข้อมูล :attribute',
    'translation_file' => 'ไม่มีไฟล์คำแปล',
    'placeholder' => 'ขาด แอตทริบิวต์ " :placeholder "',
    'image_valid' => ':attribute ต้องเป็นรูปภาพ',
    'missing_configuration_value' => 'ขาด :attribute configuration value.',
    'sms_gateway_error' => 'ไม่สามารถส่งข้อความได้   เกิดข้อผิดพลาดของ SMS เกตเวย์',
    'cant_configure_device' => 'ไม่สามารถกำหนดค่าอุปกรณ์',
    'field_does_not_exist' => ':attribute ไม่มีอยู่',
    'unsupported_rules' => 'Unsupported rules:',
    'unsupported_parameterized_rules' => 'Unsupported parameterized rules:',
    'cant_update_custom_field' => 'ไม่สามารถอัปเดตฟิลด์ " :field " ได้  เนื่องจากมีข้อมูลที่ใช้ฟิลด์นี้แล้ว',
    'strong_password' => 'รหัสผ่านที่คาดเดายาก',
    'uppercase_character' => 'ต้องใช้อักขระตัวพิมพ์ใหญ่',
    'lowercase_character' => 'ต้องใช้อักขระตัวพิมพ์เล็ก',
    'digit_character' => 'ต้องมีอักขระหลัก',
    'wrong_captcha' => 'CAPTCHA ไม่ถูกต้อง',
    'dimensions' => ':attribute มีขนาดภาพที่ไม่ถูกต้อง',
    'mimetypes' => ':attribute ต้องเป็นไฟล์ประเภท: :values',
    'in_array' => ':attribute ไม่มีอยู่ใน :other',
    'uploaded' => ':attribute ล้มเหลวในการอัปโหลด เป็นไปได้ว่าเซิร์ฟเวอร์ไม่ยอมรับขนาดดังกล่าว',
    'login_methods'  => array(
        'email' => 'อีเมล',
        'azure' => 'ไมโครซอฟต์ อาซัวร์',
    ),
    'host' => ':attribute ไม่ใช่โฮสต์ที่ถูกต้อง',
    'host_port' => ':attribute ไม่ตรงกับรูปแบบ HOST :PORT',
    'unique_table_msg' => ':attribute ได้ถูกนำไปใช้ใน :table แล้ว',
);