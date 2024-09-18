<?php

use Carbon\Carbon;
use CustomFacades\Appearance;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceSensor;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\SmsTemplate;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\SMSGatewayManager;

function solveEquation(array $values, $formula) {
    $equation = $formula;

    foreach ($values as $placeholder => $value) {
        $equation = str_replace($placeholder, $value, $equation);
    }

    $eos = new eqEOS();
    try {
        $result = $eos->solveIF($equation);
    }
    catch(\Exception $e) {
        $result = null;
    }
    return $result;
}

function getPrc($nr1, $nr2) {
    if (empty($nr1))
        return 0;

    if ($nr1 == 0)
        return 0;

    if ($nr1 < $nr2)
        return 100;
    return float(($nr2/$nr1) * 100);
}

function parseSensorsSelect($sensors) {
    $detect_engine_hours = [
        'gps' => trans('front.gps')
    ];

    $detect_engine = [
        'gps' => trans('front.gps')
    ];

    $detect_speed = [
        'gps' => trans('front.gps')
    ];

    $detect_distance = [
        'gps' => trans('front.gps')
    ];

    foreach ($sensors as $sensor) {

        $title = trans('front.sensor') . ': ' . $sensor->type_title;

        switch ($sensor->type) {
            case 'acc':
            case 'engine':
            case 'ignition':
                $detect_engine_hours[$sensor->type] = $title;
                $detect_engine[$sensor->type] = $title;
                break;
            case 'engine_hours':
                $detect_engine_hours[$sensor->type] = $title;
                break;
            case 'odometer':
                $detect_distance[$sensor->type] = $title;
                break;
            case 'speed_ecm':
                $detect_speed[$sensor->type] = $title;
                break;
        }
    }

    return [
        'detect_engine_hours'    => $detect_engine_hours,
        'detect_engine'   => $detect_engine,
        'detect_distance' => $detect_distance,
        'detect_speed'    => $detect_speed,
    ];
}

function isDemoUser($user = NULL)
{
    if (is_null($user))
        $user = Auth::User();

    return $user->isDemo();
}

function isPublic()
{
    return config('tobuli.type') == 'public';
}

function dontExist($name)
{
    return trans('global.dont_exist', ['attribute' => trans($name)]);
}

function beginTransaction()
{
    DB::beginTransaction();
}

function rollbackTransaction()
{
    DB::rollback();
}

function commitTransaction()
{
    DB::commit();
}

function modalError($message)
{
    return View::make('admin::Layouts.partials.error_modal')->with('error', trans($message));
}

function modal($message, $type = 'warning')
{
    return View::make('front::Layouts.partials.modal_warning', [
        'type' => $type,
        'message' => $message
    ]);
}

function isAdmin() {
    return Auth::User() && (Auth::User()->isAdmin() || Auth::User()->isManager());
}

function kilometersToMiles($km)
{
    return round($km / 1.609344);
}

function milesToKilometers($ml)
{
    return round($ml * 1.609344);
}

function gallonsToLiters($gallons)
{
    if ($gallons <= 0)
        return 0;

    return $gallons * 3.78541178;
}

function litersToGallons($liters)
{
    if ($liters <= 0)
        return 0;

    return $liters / 3.78541178;
}

function float($number)
{
    return number_format($number, 2, '.', FALSE);
}

function cord($number)
{
    return number_format($number, 6, '.', FALSE);
}

function convertFuelConsumption($type, $fuel_quantity)
{
    if ($fuel_quantity <= 0)
        return 0;

    if ($type == 1) {
        return $fuel_quantity / 100;
    } elseif ($type == 3 || $type == 4) {
        return $fuel_quantity;
    } elseif ($type == 2) {
        return gallonsToLiters(1) / milesToKilometers($fuel_quantity);
    } elseif ($type == 5) {
        return 1 / $fuel_quantity;
    } else {
        return 0;
    }
}

function sendTemplateEmail($to, EmailTemplate $template, $data, $attaches = [])
{
    $email = $template->buildTemplate($data);

    $result = \CustomFacades\MailHelper::fallback(true)->send($to, $email['subject'], $email['body'], $attaches);

    if (config('tobuli.log_send_mail_template')) {
        $data = [
            Carbon::now(),
            $template->name,
            $to,
            $result['status'] ? 'OK' : $result['error']
        ];

        File::append(storage_path('logs/send_email_template.log'), implode(', ', $data) . "\n");
    }

    return $result;
}


/**
 * @param $to
 * @param  SmsTemplate  $template
 * @param $data
 * @param $user
 * @return int[]|void
 * @throws ValidationException
 */
function sendTemplateSMS($to, SmsTemplate $template, $data, $user)
{
    if (empty($to) || empty($user))
        return;

    if ( ! $user instanceof User)
        $user = User::find($user);

    if ( ! $user)
        return;

    $sms = $template->buildTemplate($data);

    sendSMS($to, $sms['body'], $user);

    return ['status' => 1];
}

/**
 * @param $to
 * @param $body
 * @param $user
 * @throws ValidationException
 */
function sendSMS($to, $body, $user)
{
    if (empty($user))
        return;

    if ( ! $user->perm('sms_gateway', 'view'))
        return;

    $sms_manager = new SMSGatewayManager();

    $sms_sender_service = $sms_manager->loadSender($user);

    $sms_sender_service->send($to, $body);

    return ['status' => 1];
}

function sendWebhook($url, $data, array $headers = [], $retry = 1)
{
    $client = new \GuzzleHttp\Client();

    $postOptions = [
        GuzzleHttp\RequestOptions::TIMEOUT => config('webhook.timeout'),
        GuzzleHttp\RequestOptions::JSON => $data,
    ];

    if (count($headers)) {
        $postOptions[\GuzzleHttp\RequestOptions::HEADERS] = $headers;
    }

    try {
        $response = $client->post($url, $postOptions);
    } catch (Exception $e) {
        $response = null;
        $error = $e->getMessage();
    }

    if ($response && in_array($response->getStatusCode(), [200, 201, 202, 203, 204, 205]))
        return;

    if (config('webhook.log')) {
        $line = [
            Carbon::now(),
            $response ? $response->getStatusCode() : '',
            $url,
            $error ?? '',
            json_encode($data)
        ];

        File::append(storage_path('webhook.log'), implode(' - ', $line) . "\n");
    }

    if (config('webhook.retry') > $retry)
        sendWebhook($url, $data, $headers, ++$retry);
}

function formatBytes($bytes)
{
    $bytes = empty($bytes) ? 0 : $bytes;

    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    $bytes /= (1 << (10 * $pow));

    switch (true) {
        case ($bytes > 100):
            $precision = 0;
            break;
        case ($bytes > 10):
            $precision = 1;
            break;
        case ($bytes > 1):
            $precision = 2;
            break;
        default:
            $precision = 0;
    }

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function timezoneReverse($zone)
{
    if (strpos($zone, '+') !== FALSE)
        $zone = str_replace('+', '-', $zone);
    else
        $zone = str_replace('-', '+', $zone);
    return $zone;
}

function checkCondition($type, $text, $tag_value)
{
    if ($type == 1 && $text == $tag_value)
        return TRUE;

    $value_number = parseNumber($text);

    if ($type == 2 && is_numeric($value_number) && $value_number > $tag_value)
        return TRUE;

    if ($type == 3 && is_numeric($value_number) && $value_number < $tag_value)
        return TRUE;

    return FALSE;
}

function getGeoAddress($lat, $lon)
{
    return CustomFacades\GeoLocation::resolveAddress($lat, $lon);
}

function prepareServiceData($input, $values = NULL)
{
    $last_service = $input['last_service'];

    if ($input['expiration_by'] == 'days') {
        $today = Carbon::parse(Formatter::time()->convert(Carbon::now()));
        $last_service = Carbon::parse($last_service);
        $input['last_service'] = $last_service->copy();
        $input['expires_date'] = $last_service->copy()
            ->addDays($input['interval']);

        if ($today >= $input['expires_date'] && isset($input['renew_after_expiration'])) {
            $diff = $today->diffInDays($last_service);
            $times = floor($diff / $input['interval']);
            $daysTillExpiration = $input['interval'] - ($times > 0 ? ($diff - $input['interval'] * $times) : 0);

            $input['expires_date'] = $today->copy()
                ->addDays($daysTillExpiration);
            $input['last_service'] = $input['expires_date']->copy()
                ->subDays($input['interval']);
            $input['event_sent'] = 0;
        }

        $input['trigger_event_left'] = ! empty($input['trigger_event_left'])
            ? $input['trigger_event_left']
            : 0;
        $input['remind_date'] = $input['expires_date']->copy()
            ->subDays($input['trigger_event_left']);

        $input['expired'] = $today >= $input['expires_date'];
        $input['event_sent'] = $today >= $input['remind_date'];

        $input['expires_date'] = $input['expires_date']->format('Y-m-d');
        $input['remind_date'] = $input['remind_date']->format('Y-m-d');
        $input['last_service'] = $input['last_service']->format('Y-m-d');
    } else {
        $value = $values[$input['expiration_by']];
        $input['last_service'] = (is_numeric($last_service) && $last_service > 0) ? $last_service : 0;
        $input['expires'] = $input['interval'] + $input['last_service'];

        if ($value >= $input['expires'] && isset($input['renew_after_expiration'])) {
            $over = $value - $input['expires'];
            $times = ceil($over / $input['interval']);
            $input['expires'] = $input['expires'] + ($input['interval'] * ($times > 0 ? $times : 1));
            $input['last_service'] = $input['expires'] - $input['interval'];
            $input['event_sent'] = 0;
        }

        $input['remind'] = $input['expires'] - $input['trigger_event_left'];

        $input['expired'] = ($value >= $input['expires']);
        $input['event_sent'] = ($value >= $input['remind']);
    }

    return $input;
}

function getDatabaseSize($db_name)
{
    if (\CustomFacades\Server::isDatabaseLocal()) {
        $results = DB::select(DB::raw('SHOW VARIABLES WHERE Variable_name = "datadir" OR Variable_name = "innodb_file_per_table"'));

        if (empty($results))
            return 0;

        foreach ($results as $variable) {
            if ($variable->Variable_name == 'datadir')
                $dir = $variable->Value;
            if ($variable->Variable_name == 'innodb_file_per_table')
                $innodb_file_per_table = $variable->Value == 'ON' ? true : false;
        }

        if (empty($innodb_file_per_table)) {
            if (empty($dir))
                return 0;

            return exec("du -msh -B1 $dir | cut -f1");
        }
    }

    //calc via DB query (very slow)

    if (is_array($db_name)) {
        $dbs = $db_name;
    } else {
        $dbs = [$db_name];
    }

    $where = '';
    foreach ($dbs as $db) {
        $where .= '"' . $db . '",';
    }
    $where = trim($where, ',');

    $res = DB::select(DB::raw('SELECT table_schema, SUM( data_length + index_length) AS db_size FROM information_schema.TABLES WHERE table_schema IN (' . $where . ');'));

    if (empty($res))
        return 0;

    return current($res)->db_size;
}

function getMaps()
{
    $maps = Config::get('tobuli.maps');
    if (isset($_ENV['use_slovakia_map']))
        $maps['Tourist map Slovakia'] = 99;
    if (isset($_ENV['use_singapure_map']))
        $maps['One Map Singapure'] = 98;
    ksort($maps);
    return array_flip($maps);
}

function getAvailableMaps()
{
    $maps = getMaps();
    $available_maps = settings('main_settings.available_maps');

    return array_filter($maps, function($map_id) use ($available_maps){
        return in_array($map_id, $available_maps);
    }, ARRAY_FILTER_USE_KEY );
}

function images_path($path = '')
{
    if ($path) {
        $path = DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    return "/var/www/html/images{$path}";
}

function asset_resource($path, $file = null)
{
    if (is_null($file))
        $file = public_path($path);

    $url = asset($path);

    if (file_exists($file))
        $url .= '?t=' . filemtime($file);

    return $url;
}

function asset_flag($lang)
{
    $languages = settings('languages');

    if (empty($languages[$lang]['flag']))
        return asset("assets/images/header/en.png");

    return asset("assets/images/header/{$languages[$lang]['flag']}");
}

function getMainPermission($name, $mode)
{
    $mode = trim($mode);
    $modes = Config::get('tobuli.permissions_modes');

    if (!array_key_exists($mode, $modes))
        die('Bad permission');

    $user_permissions = settings('main_settings.user_permissions');

    return $user_permissions && array_key_exists($name, $user_permissions) ? boolval($user_permissions[$name][$mode]) : FALSE;
}

function calibrate($number, $x1, $y1, $x2, $y2)
{
    if ($number == $x1)
        return $y1;

    if ($number == $x2)
        return $y2;


    if ($x1 > $x2) {
        $nx1 = $x1;
        $nx2 = $x2;
    } else {
        $nx1 = $x2;
        $nx2 = $x1;
    }

    if ($y1 > $y2) {
        $ny1 = $y1;
        $ny2 = $y2;
        $pr = $x2;
    } else {
        $ny1 = $y2;
        $ny2 = $y1;
        $pr = $x1;
    }


    $sk = ($pr - $number);
    $sk = $sk < 0 ? -$sk : $sk;

    return (($ny1 - $ny2) / ($nx1 - $nx2)) * $sk + $ny2;
}

function radians($deg)
{
    return $deg * M_PI / 180;
}

function getDistance($latitude, $longitude, $last_latitude, $last_longitude)
{
    if (is_null($latitude) || is_null($longitude) || is_null($last_latitude) || is_null($last_longitude))
        return 0;

    $latitude = (float)$latitude;
    $longitude = (float)$longitude;
    $last_latitude = (float)$last_latitude;
    $last_longitude = (float)$last_longitude;

    if (($latitude == 0 && $longitude == 0) || ($last_latitude == 0 && $last_longitude == 0))
        return 0;

    if ($latitude == $last_latitude && $longitude == $last_longitude)
        return 0;

    $result = rad2deg((acos(cos(radians($last_latitude)) * cos(radians($latitude)) * cos(radians($last_longitude) - radians($longitude)) + sin(radians($last_latitude)) * sin(radians($latitude))))) * 111.045;
    if (is_nan($result))
        $result = 0;

    return $result;
}

function getCourse($latitude, $longitude, $last_latitude, $last_longitude)
{
    //difference in longitudinal coordinates
    $dLon = deg2rad((float)$longitude) - deg2rad((float)$last_longitude);

    //difference in the phi of latitudinal coordinates
    $dPhi = log(tan(deg2rad((float)$latitude) / 2 + pi() / 4) / tan(deg2rad((float)$last_latitude) / 2 + pi() / 4));

    //we need to recalculate $dLon if it is greater than pi
    if (abs($dLon) > pi()) {
        if ($dLon > 0) {
            $dLon = (2 * pi() - $dLon) * -1;
        } else {
            $dLon = 2 * pi() + $dLon;
        }
    }

    //return the angle, normalized
    return (rad2deg(atan2($dLon, $dPhi)) + 360) % 360;
}

function parseNumber($string)
{

    preg_match("/-?((?:[0-9]+,)*[0-9]+(?:\.[0-9]+)?)/", $string, $matches);
    if (isset($matches['0']))
        return $matches['0'];

    return '';
}

function apiArray($arr)
{
    $result = [];
    foreach ($arr as $id => $value)
        array_push($result, ['id' => $id, 'value' => $value, 'title' => $value]);

    return $result;
}

function toOptions(Array $array)
{
    $result = [];

    foreach ($array as $id => $value)
        array_push($result, ['id' => $id, 'title' => $value]);

    return $result;
}

function snapToRoad($positions)
{
    if (empty($positions))
        return $positions;

    if (count($positions) < 2)
        return $positions;

    $path = implode('|', array_map(function($position) {
        return "{$position->latitude},{$position->longitude}";
    }, $positions));

    $response = callSnapToRoad($path);

    if (empty($response['snappedPoints']))
        return $positions;

    $result = [];

    foreach ($positions as $i => $position) {
        do {
            $snapped = array_shift($response['snappedPoints']);

            if ($snapped) {
                $position->latitude = $snapped['location']['latitude'];
                $position->longitude = $snapped['location']['longitude'];
            }

            if ($snapped && !isset($snapped['originalIndex'])) {
                $result[] = $position;
            }

        } while ($snapped && !isset($snapped['originalIndex']));

        $result[] = $position;
    }

    return $result;
}

function callSnapToRoad($path)
{
    static $key = null;

    if (is_null($key))
        $key = config('services.snaptoroad.key');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://roads.googleapis.com/v1/snapToRoads?path={$path}&interpolate=true&key={$key}");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HEADER, 0);

    $response = @json_decode(curl_exec($ch), true);

    curl_close($ch);

    if ( ! isset($response['snappedPoints']))
        return null;

    return $response;
}

function gen_polygon_text($items)
{
    $cor_text = NULL;

    if (empty($items))
        return $cor_text;

    foreach ($items as $item) {
        $cor_text .= $item['lat'] . ' ' . $item['lng'] . ',';
    }

    if ($item['lat'] != $items['0']['lat'] || $item['lng'] != $items['0']['lng'])
        $cor_text .= $items['0']['lat'] . ' ' . $items['0']['lng'];
    else
        $cor_text = substr($cor_text, 0, -1);

    return $cor_text;
}

function cmpdate($a, $b)
{
    return strcmp($b['date'], $a['date']);
}

function rcmp($a, $b)
{
    return strcmp($b['sort'], $a['sort']);
}

function cmp($a, $b)
{
    return strcmp($a['sort'], $b['sort']);
}

function stripInvalidXml($value)
{
    $ret = "";
    if (empty($value)) {
        return $ret;
    }

    $length = strlen($value);
    for ($i = 0; $i < $length; $i++) {
        $current = ord($value[$i]);
        if (($current == 0x9) ||
            ($current == 0xA) ||
            ($current == 0xD) ||
            (($current >= 0x20) && ($current <= 0xD7FF)) ||
            (($current >= 0xE000) && ($current <= 0xFFFD)) ||
            (($current >= 0x10000) && ($current <= 0x10FFFF))) {
            $ret .= chr($current);
        } else {
            $ret .= " ";
        }
    }
    return $ret;
}

function parseXML($text)
{
    $arr = [];
    $text = stripInvalidXml($text);

    try {
        $xml = new \SimpleXMLElement($text);
    } catch (\Exception $e) {
        $xml = FALSE;
    }

    if (empty($xml))
        return $arr;

    foreach ($xml as $key => $value) {
        if (is_array($value))
            continue;
        $arr[] = htmlentities($key) . ': ' . htmlentities($value);
    }

    return $arr;
}

function parsePorts($ports = NULL)
{
    $url = config('app.url');
    if (empty($ports)) {
        $curl = new \Curl;
        $curl->follow_redirects = false;
        $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;

        $ports = json_decode($curl->get($url.'/data/ports.php'), TRUE);
    }
    $arr = [];
    foreach ($ports as $port)
        $arr[$port['name']] = $port;
    $ports = $arr;
    unset($arr);

    $cur_ports = json_decode(json_encode(DB::table('tracker_ports')->get()->all()), TRUE);
    $arr = [];
    foreach ($cur_ports as $port) {
        if (!isset($ports[$port['name']])) {
            DB::table('tracker_ports')->where('name', '=', $port['name'])->delete();
            continue;
        }
        $arr[$port['name']] = $port;
    }
    $cur_ports = $arr;
    unset($arr);

    foreach ($ports as $port) {
        if (!isset($cur_ports[$port['name']])) {
            while (!empty(DB::table('tracker_ports')->where('port', '=', $port['port'])->first())) {
                $port['port']++;
            }
            DB::table('tracker_ports')->insert([
                'name' => $port['name'],
                'port' => $port['port'],
                'extra' => $port['extra']
            ]);
        } else {
            $extras = json_decode($port['extra'], TRUE);
            if (!empty($extras)) {
                $cur_extras = json_decode($cur_ports[$port['name']]['extra'], TRUE);
                $update = FALSE;
                foreach ($extras as $key => $value) {
                    if (!isset($cur_extras[$key])) {
                        $cur_extras[$key] = $value;
                        $update = TRUE;
                    }
                }

                if ($update) {
                    DB::table('tracker_ports')->where('name', '=', $port['name'])->update([
                        'extra' => json_encode($cur_extras)
                    ]);
                }
            }
        }
    }
}

function updateUsersBillingPlan($current_plan_id, $new_plan_id)
{
    $plan = \Tobuli\Entities\BillingPlan::find($new_plan_id);

    if ($plan) {
        $update = [
            'billing_plan_id' => $new_plan_id,
            'devices_limit' => $plan->objects,
            'subscription_expiration' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " + {$plan->duration_value} {$plan->duration_type}"))
        ];

        DB::table('users')
            ->whereNull('billing_plan_id')
            ->where('group_id', '=', 2)
            ->update($update);

    } else {
        DB::table('users')
            ->where('billing_plan_id', $current_plan_id)
            ->where('group_id', '=', 2)
            ->update([
                'billing_plan_id' => NULL,
            ]);
    }
}

class SettingsArray
{
    private $array = [];

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function getArray()
    {
        return $this->array;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->array))
            return $this->array[$name] == 1 ? TRUE : FALSE;

        return TRUE;
    }
}

function parseXMLToArray($text)
{
    $arr = [];
    try {
        $text = stripInvalidXml($text);
        $xml = new \SimpleXMLElement($text);
        foreach ($xml as $key => $value) {
            if (is_array($value))
                continue;
            $arr[htmlentities($key)] = htmlentities($value);
        }
    } catch (Exception $e) {
        $arr = parseInvalidXMLToArray($text);
    }

    return $arr;
}

function parseInvalidXMLToArray($text)
{
    $prefix = '_';
    $arr = [];
    try {
        $text = preg_replace('/<(\/?)([^<>]+)>/', '<$1' . $prefix . '$2>', $text);
        $xml = new \SimpleXMLElement($text);
        foreach ($xml as $key => $value) {
            if (is_array($value))
                continue;

            $key = ltrim($key, $prefix);

            $arr[htmlentities($key)] = htmlentities($value);
        }
    } catch (Exception $e) {
    }

    return $arr;
}

/**
 * @param array $array1
 * @param array $array2
 * @return array
 */
function array_merge_recursive_distinct(array &$array1, array &$array2)
{
    $merged = $array1;

    foreach ($array2 as $key => &$value) {
        if (is_array($value) && is_array_assoc($value) && isset ($merged [$key]) && is_array($merged [$key])) {
            $merged [$key] = array_merge_recursive_distinct($merged [$key], $value);
        } else {
            $merged [$key] = $value;
        }
    }

    return $merged;
}

function teltonikaIbutton($str)
{
    $str = dechex($str);
    if (!is_int(strlen($str) / 2))
        $str = '0' . $str;

    $arr = str_split(strrev($str), 2);
    $res = '';
    foreach ($arr as $item) {
        $res .= strrev($item);
    }

    return $res;
}

function listviewTrans($user_id, &$settings, &$fields)
{
    $fields_trans = config('tobuli.listview_fields_trans');
    $sensors_trans = config('tobuli.sensors');

    $sensors = \CustomFacades\Repositories\DeviceSensorRepo::whereUserId($user_id);

    foreach ($sensors as $sensor) {
        $hash = $sensor->hash;

        $fields[$hash] = [
            'field' => $hash,
            'class' => 'sensor',
            'type' => $sensor->type,
            'name' => $sensor->name
        ];

        if (isset($settings['columns'])) {
            foreach ($settings['columns'] as &$column) {
                if ($column['field'] != $hash)
                    continue;

                $column['title'] = $sensor->name . " (" . $column['title'] . ")";
            }
        }
    }

    foreach ($fields as &$field) {
        if ($field['class'] == 'sensor') {
            $field['title'] = $field['name'] . " (" . Arr::get($sensors_trans, $field['type'], 'none') . ")";
        } else {
            $field['title'] = $fields_trans[$field['field']];
        }

        $field['title'] = htmlentities($field['title'], ENT_QUOTES);
    }
}

function has_array_value($array, $keys)
{
    if (empty($keys))
        return true;

    $key = array_shift($keys);

    if (array_key_exists($key, $array))
        return has_array_value($array[$key], $keys);
    else
        return false;
}

function get_array_value($array, $keys)
{
    if (empty($keys))
        return $array;

    $key = array_shift($keys);

    if (array_key_exists($key, $array))
        return get_array_value($array[$key], $keys);
    else
        return null;
}

function set_array_value(&$array, $keys, $value)
{
    if (empty($keys))
        return $array = $value;

    if (is_null($array))
        $array = [];

    $key = array_shift($keys);

    if (!array_key_exists($key, $array))
        $array[$key] = null;

    return set_array_value($array[$key], $keys, $value);
}

function array_sort_array(array $array, array $orderArray)
{
    $ordered = array();

    foreach ($orderArray as $key) {
        if (array_key_exists($key, $array)) {
            $ordered[$key] = $array[$key];
            unset($array[$key]);
        }
    }

    return $ordered + $array;
}

function is_array_assoc(array $array)
{
    if (array() === $array)
        return false;

    return array_keys($array) !== range(0, count($array) - 1);
}


function semicol_explode($input)
{
    $values = explode(';', $input);
    $values = array_map('trim', $values);
    return array_filter($values, function ($value) {
        return !empty($value);
    });
}

function appendRulesArray(array &$rules, array $newRules)
{
    foreach ($newRules as $field => $fieldRules) {
        if (!isset($rules[$field])) {
            $rules[$field] = $fieldRules;

            continue;
        }

        if (is_string($fieldRules)) {
            $fieldRules = explode('|', $fieldRules);
        }

        if (is_string($rules[$field])) {
            $rules[$field] = explode('|', $rules[$field]);
        }

        $rules[$field] = implode('|', array_merge($fieldRules, $rules[$field]));
    }
}

function tooltipMark($content, $options = [])
{
    $defaults = [
        'toggle' => 'tooltip',
        'html' => true,
        'title' => $content
    ];

    $options = array_merge($defaults, $options);

    $attributes = '';

    foreach ($options as $key => $value)
        $attributes .= "data-$key='$value' ";

    return '<span class="tooltip-mark" ' . $attributes . '>?</span>';
}

function tooltipMarkImei($img, $text)
{
    $options = [
        'template' => '<div class="tooltip tooltip-imei" role="tooltip"><div class="tooltip-inner" style="background-image: url(' . $img . ');"></div></div>'
    ];

    return tooltipMark('<span class="text">' . $text . '</span>', $options);
}

function tooltipMarkImg($img)
{
    $options = [
        'template' => '<div class="tooltip tooltip-img" role="tooltip"><div class="tooltip-inner"></div></div>'
    ];

    return tooltipMark('<img src="' . $img . '"/>', $options);
}

function propertyPolicy($entity)
{
    return app(\App\Policies\Property\PropertyPolicyManager::class)->policyFor($entity);
}

function actionPolicy($action)
{
    return app(\App\Policies\Action\ActionPolicyManager::class)
        ->policyFor($action);
}

function onlyEditables($entity, $user, $input)
{
    $not_editables = propertyPolicy($entity)->notEditables($user, $entity);

    if (empty($not_editables))
        return $input;

    foreach ($not_editables as $property) {
        if ( ! array_key_exists($property, $input))
            continue;

        $input[$property] = $entity->$property;
    }

    return $input;
}

function getUserDashboardSettings($user)
{
    if (empty($user_config = $user->getSettings('dashboard')))
        $user_config = [];

    $config = config('tobuli.dashboard');

    return array_merge_recursive_distinct($config, $user_config);
}

function convertPHPToMomentFormat($format)
{
    $replacements = [
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '', // no equivalent
        'L' => '', // no equivalent
        'o' => 'YYYY',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'B' => '', // no equivalent
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz', // deprecated since version 1.6.0 of moment.js
        'I' => '', // no equivalent
        'O' => '', // no equivalent
        'P' => '', // no equivalent
        'T' => '', // no equivalent
        'Z' => '', // no equivalent
        'c' => '', // no equivalent
        'r' => '', // no equivalent
        'U' => 'X',
    ];
    $momentFormat = strtr($format, $replacements);
    return $momentFormat;
}

/**
 * @link https://github.com/flot/flot/blob/master/API.md#time-series-data
 */
function convertPhpToFlotDateFormat(string $format): string
{
    $replacements = [
        'Y' => '%Y',
        'y' => '%y',
        'm' => '%m',
        'd' => '%d',
        'H' => '%H',
        'h' => '%I',
        'i' => '%M',
        's' => '%S',
        'A' => '%P',
    ];

    return strtr($format, $replacements);
}

function getPointZones($geofences, $lat, $lng)
{
    $zones = [];

    if (is_null($geofences))
        return $zones;

    foreach ($geofences as $geofence) {
        if ( ! $geofence->pointIn(['latitude' => $lat, 'longitude' => $lng]))
            continue;

        $zones[] = $geofence->name;
    }

    return $zones;
}

function getSortedWeekdays()
{
    $weekdays = config('tobuli.weekdays');
    $day = auth()->user()->week_start_weekday;

    foreach ($weekdays as $weekday => $value) {
        if ($weekday == $day)
            break;

        Arr::pull($weekdays, $weekday);
        $weekdays = $weekdays + [$weekday => $value];
    }

    return $weekdays;
}

function googleMapLink($latitude, $longidute, $text = null)
{
    if (is_null($text))
        $text = "{$latitude}, {$longidute}";

    $url = googleMapUrl($latitude, $longidute);

    return "<a href='{$url}' target='_blank'>{$text}</a>";
}

function googleMapUrl($latitude, $longidute)
{
    return "https://maps.google.com/maps?q={$latitude},{$longidute}";
}

function runCacheEntity($entityClass, $ids)
{
    $list = new \Illuminate\Database\Eloquent\Collection();

    if (empty($ids))
        return $list;

    if ( ! is_array($ids))
        $ids = [$ids];

    foreach ($ids as $id) {
        $entity = Cache::store('array')->rememberForever("$entityClass.$id", function() use ($entityClass, $id) {
            return $entityClass::find($id);
        });

        if ($entity)
            $list->add($entity);
    }

    return $list;
}

function groupDevices($devices, $user)
{
    $device_groups = CustomFacades\Repositories\DeviceGroupRepo::getWhere(['user_id' => $user->id], 'title')
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

    $grouped = [];

    foreach ($devices as $device) {
        $group_id = (!is_null($device->pivot)) ? $device->pivot->group_id : $device->group_id;
        $group_id = (is_null($group_id) || (!Arr::has($device_groups, $group_id))) ? 0 : $group_id;
        $grouped[$device_groups[$group_id]][$device->id] = $device->name;
    }

    return $grouped;
}

function groupPois($pois, $user)
{
    $groups = CustomFacades\Repositories\PoiGroupRepo::getWhere(['user_id' => $user->id], 'title')
        ->pluck('title', 'id')
        ->prepend(trans('front.ungrouped'), '0')
        ->all();

    $grouped = [];

    foreach ($pois as $poi) {
        $group_id = $poi->group_id;
        $group_id = (is_null($group_id) || (!Arr::has($groups, $group_id))) ? 0 : $group_id;
        $grouped[$groups[$group_id]][$poi->id] = $poi->name;
    }

    return $grouped;
}

function parseTagValue($string, $tag)
{
    $value = NULL;

    if (empty($tag))
        return $value;

    preg_match('/<' . preg_quote($tag, '/') . '>(.*?)<\/' . preg_quote($tag, '/') . '>/s', $string, $matches);
    if (isset($matches['1']))
        $value = $matches['1'];

    return $value;
}

function expensesTypesExist()
{
    $count = Illuminate\Support\Facades\Cache::get('expenses_types_count');

    if (is_null($count))
    {
        $count = \Tobuli\Entities\DeviceExpensesType::count();

        Illuminate\Support\Facades\Cache::put('expenses_types_count', $count, 1440 * 60);
    }

    if ($count > 0)
        return true;

    return false;
}

function removeEmoji($string) {

    // Match Emoticons
    $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clear_string = preg_replace($regex_emoticons, '', $string);

    // Match Miscellaneous Symbols and Pictographs
    $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clear_string = preg_replace($regex_symbols, '', $clear_string);

    // Match Transport And Map Symbols
    $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clear_string = preg_replace($regex_transport, '', $clear_string);

    // Match Miscellaneous Symbols
    $regex_misc = '/[\x{2600}-\x{26FF}]/u';
    $clear_string = preg_replace($regex_misc, '', $clear_string);

    // Match Dingbats
    $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
    $clear_string = preg_replace($regex_dingbats, '', $clear_string);

    return $clear_string;
}

/**
 * Replaces new lines with specified value and trims string
 *
 * @param  String  $string   String to be modified
 * @param  String  $newValue Value to replace new lines
 * @return String
 */
function replaceNewlines($string, $newValue)
{
    return trim(str_replace(["\r\n", "\r", "\n"], $newValue, $string));
}

/**
 * Exports variable to string
 *
 * @param  Mixed   $var             Variable to export
 * @param  String  $replaceNewlines Value to replace newlines in strings
 * @param  String  $indent          Starting indentation
 * @return String
 */
function exportVar($var, $replaceNewlines = "\n", $indent = '')
{
    switch (gettype($var)) {
        case "string":
            return "'". addcslashes(replaceNewlines($var, $replaceNewlines), "'") . "'";
        case "array":
            $indexed = array_keys($var) === range(0, count($var) - 1);
            $r = [];

            foreach ($var as $key => $value) {
                $r[] = "$indent    "
                        . ($indexed ? "" : exportVar($key, $replaceNewlines) . " => ")
                        . exportVar($value, $replaceNewlines, "$indent    ");
            }

            return "[\n" . implode(",\n", $r) . ",\n" . $indent . "]";
        case "boolean":
            return $var ? "true" : "false";
        default:
            return var_export($var, true);
    }
}

/**
 * Converts dot notation array to multidimensional array
 *
 * @param  Array $array Dot notation array
 * @return Array
 */
function array_undot($array)
{
    $result = [];

    foreach ($array as $key => $value) {
        Arr::set($result, $key, $value);
    }

    return $result;
}

function getSensorFuelDifference(DeviceSensor $sensor, $positions, $default = null): array
{
    $first = $default;
    $last = $default;
    $min = null;
    $max = null;
    $minPosition = null;
    $maxPosition = null;

    foreach ($positions as $position) {
        $last = $sensor->getValuePosition($position, $last);

        if ($first === null) {
            $first = $last;
        }

        if ($last !== null && ($last > $max || $max === null)) {
            $max = $last;
            $maxPosition = $position;
        }

        if ($last !== null && ($last < $min || $min === null)) {
            $min = $last;
            $minPosition = $position;
        }
    }

    if ($first < 0 || $last < 0)
        throw new \Exception('Negative fuel value');

    $difference = $last - $first;

    $bigger = max($first, $last);

    $result = [
        'percent'       =>  $bigger ? ($difference / abs($bigger)) * 100 : 0,
        'difference'    =>  $difference,
        'increased'     =>  $first < $last,
        'first_value'   =>  $first,
        'last_value'    =>  $last,
    ];

    if ($result['increased']) {
        $result['edge_value'] = $max;
        $result['edge_position'] = $maxPosition;
    } else {
        $result['edge_value'] = $min;
        $result['edge_position'] = $minPosition;
    }

    $bigger = max($first, $result['edge_value']);

    $result['edge_difference'] = $result['edge_value'] - $first;
    $result['edge_percent'] = $bigger ? $result['edge_difference'] / $bigger * 100 : 0;

    return $result;
}

/**
 * Return device camera's image path
 *
 * @param string $path Path relative to images location
 * @return string
 */
function cameras_media_path($path = '')
{
    $config = config('tracker');

    return Str::finish($config['media.path'], '/').($path ?? '');
}

function listsTranslations()
{
    Config::set('lists.users_groups', [
        '1' => trans('admin.group_1'),
        '6' => trans('admin.group_6'),
        '3' => trans('admin.group_3'),
        '5' => trans('admin.group_5'),
        '2' => trans('admin.group_2'),
        '4' => trans('admin.group_4'),
    ]);

    $sensors = [
        'satellites' => trans('front.satellites'),
        'gsm' => trans('front.gsm'),
        'engine' => trans('front.engine_on_off'),
        'acc' => trans('front.acc_on_off'),
        'door' => trans('front.door_on_off'),
        'seatbelt' => trans('front.seatbelt_on_off'),
        'battery' => trans('front.battery'),
        'fuel_tank' => trans('front.fuel_tank'),
        'fuel_tank_calibration' => trans('front.fuel_tank_calibration'),
        'fuel_consumption' => trans('front.fuel_consumption'),
        'temperature' => trans('front.temperature'),
        'temperature_calibration' => trans('front.temperature_calibration'),
        'odometer' => trans('front.odometer'),
        'tachometer' => trans('front.tachometer'),
        'ignition' => trans('front.ignition_on_off'),
        'engine_hours' => trans('validation.attributes.engine_hours'),
        'harsh_acceleration' => trans('front.harsh_acceleration'),
        'harsh_breaking' => trans('front.harsh_breaking'),
        'harsh_turning' => trans('front.harsh_turning'),
        'logical' => trans('front.logical'),
        'numerical' => trans('front.numerical'),
        'textual' => trans('front.textual'),
        'route_color' => trans('front.route_color'),
        'route_color_2' => trans('front.route_color') . ' 2',
        'route_color_3' => trans('front.route_color') . ' 3',
        'load' => trans('front.load'),
        'load_calibration' => trans('front.load_calibration'),
        'speed_ecm' => trans('front.speed') . ' ECM',
        'rfid' => trans('validation.attributes.rfid'),
        'counter' => trans('front.counter'),
    ];
    if ( settings('plugins.business_private_drive.status') ) {
        $sensors['drive_business'] = trans('front.drive_business');
        $sensors['drive_private'] = trans('front.drive_private');
    }

    if ( settings('plugins.route_color.status') ) {
        $sensors['route_color'] = trans('front.route_color');
    }
    Config::set('tobuli.sensors', $sensors);


    Config::set('tobuli.units_of_distance', [
        'km' => trans('front.kilometer'),
        'mi' => trans('front.mile'),
        'nm' => trans('front.nautical_mile')
    ]);
    Config::set('tobuli.units_of_capacity', [
        'lt' => trans('front.liter'),
        'gl' => trans('front.gallon')
    ]);
    Config::set('tobuli.units_of_altitude', [
        'mt' => trans('front.meter'),
        'ft' => trans('front.feet')
    ]);
    Config::set('tobuli.object_online_timeouts', [
        '1' => '1 '.trans('front.minute_short'),
        '2' => '2 '.trans('front.minute_short'),
        '3' => '3 '.trans('front.minute_short'),
        '5' => '5 '.trans('front.minute_short'),
        '6' => '6 '.trans('front.minute_short'),
        '7' => '7 '.trans('front.minute_short'),
        '8' => '8 '.trans('front.minute_short'),
        '9' => '9 '.trans('front.minute_short'),
        '10' => '10 '.trans('front.minute_short'),
        '15' => '15 '.trans('front.minute_short'),
        '30' => '30 '.trans('front.minute_short'),
        '45' => '45 '.trans('front.minute_short'),
        '60' => '1 '.trans('front.hour_short'),
        '120' => '2 '.trans('front.hour_short'),
        '180' => '3 '.trans('front.hour_short'),
        '240' => '4 '.trans('front.hour_short'),
        '300' => '5 '.trans('front.hour_short'),
        '360' => '6 '.trans('front.hour_short'),
        '420' => '7 '.trans('front.hour_short'),
        '480' => '8 '.trans('front.hour_short'),
        '540' => '9 '.trans('front.hour_short'),
        '600' => '10 '.trans('front.hour_short'),
        '660' => '11 '.trans('front.hour_short'),
        '720' => '12 '.trans('front.hour_short'),
        '900' => '15 '.trans('front.hour_short'),
        '1080' => '18 '.trans('front.hour_short'),
        '1260' => '21 '.trans('front.hour_short'),
        '1440' => '24 '.trans('front.hour_short'),
    ]);
    Config::set('tobuli.stops_minutes', [
        '1' => '> 1 '.trans('front.minute_short'),
        '2' => '> 2 '.trans('front.minute_short'),
        '3' => '> 3 '.trans('front.minute_short'),
        '4' => '> 4 '.trans('front.minute_short'),
        '5' => '> 5 '.trans('front.minute_short'),
        '10' => '> 10 '.trans('front.minute_short'),
        '15' => '> 15 '.trans('front.minute_short'),
        '20' => '> 20 '.trans('front.minute_short'),
        '30' => '> 30 '.trans('front.minute_short'),
        '60' => '> 1 '.trans('front.hour_short'),
        '120' => '> 2 '.trans('front.hour_short'),
        '300' => '> 5 '.trans('front.hour_short'),
    ]);
    Config::set('tobuli.stops_seconds', [
        '5' => '> 5 '.trans('front.second_short'),
        '10' => '> 10 '.trans('front.second_short'),
        '15' => '> 15 '.trans('front.second_short'),
        '30' => '> 30 '.trans('front.second_short'),
        '60' => '> 1 '.trans('front.minute_short'),
        '120' => '> 2 '.trans('front.minute_short'),
        '180' => '> 3 '.trans('front.minute_short'),
        '240' => '> 4 '.trans('front.minute_short'),
        '300' => '> 5 '.trans('front.minute_short'),
        '600' => '> 10 '.trans('front.minute_short'),
        '900' => '> 15 '.trans('front.minute_short'),
        '1200' => '> 20 '.trans('front.minute_short'),
        '1800' => '> 30 '.trans('front.minute_short'),
        '3600' => '> 1 '.trans('front.hour_short'),
        '7200' => '> 2 '.trans('front.hour_short'),
        '18000' => '> 5 '.trans('front.hour_short'),
    ]);

    Config::set('tobuli.listview_fields_trans', [
        'name' => trans('validation.attributes.name'),
        'imei' => trans('validation.attributes.imei'),
        'status' => trans('validation.attributes.status'),
        'address' => trans('front.address'),
        'protocol' => trans('front.protocol'),
        'position' => trans('front.position'),
        'time' => trans('admin.last_connection'),
        'sim_number' => trans('validation.attributes.sim_number'),
        'device_model' => trans('validation.attributes.device_model'),
        'plate_number' => trans('validation.attributes.plate_number'),
        'vin' => trans('validation.attributes.vin'),
        'registration_number' => trans('validation.attributes.registration_number'),
        'object_owner' => trans('validation.attributes.object_owner'),
        'additional_notes' => trans('validation.attributes.additional_notes'),
        'group' => trans('validation.attributes.group_id'),
        'speed' => trans('front.speed'),
        'fuel' => trans('front.fuel'),
        'route_color' => trans('front.route_color'),
        'route_color_2' => trans('front.route_color') . ' 2',
        'route_color_3' => trans('front.route_color') . ' 3',
        'stop_duration' => trans('front.stop_duration'),
        'idle_duration' => trans('front.idle_duration'),
        'ignition_duration' => trans('front.ignition_duration'),
        'last_event_title' => trans('front.last_event_title'),
        'last_event_type' => trans('front.last_event_type'),
        'last_event_time' => trans('front.last_event_time'),
        'sim_activation_date' => trans('validation.attributes.sim_activation_date'),
        'sim_expiration_date' => trans('validation.attributes.sim_expiration_date'),
        'installation_date' => trans('validation.attributes.installation_date'),
        'expiration_date' => trans('validation.attributes.expiration_date'),
    ]);

    $widgetsData = [
        'device' => trans('front.object'),
        'sensors' => trans('front.sensors'),
        'services' => trans('front.services'),
        'streetview' => trans('front.street_view'),
        'location' => trans('front.location'),
        'camera' => trans('front.device_camera'),
        'image' => trans('front.device_image'),
        'fuel_graph' => trans('front.fuel'),
        'gprs_command' => trans('front.gprs_command'),
        'recent_events' => trans('front.recent_events'),
        'driver' => trans('front.driver'),
    ];

    if ( ! settings('main_settings.streetview_key') && (settings('main_settings.streetview_api') != 'default')) {
        unset($widgetsData[array_search('streetview', $widgetsData)]);
    }

    if (settings('plugins.locking_widget.status')) {
        $widgetsData['locking'] = trans('front.locking_widget');
    }

    if (config('addon.widget_template')) {
        $widgetsData['template_webhook'] = trans('front.template_webhook');
    }

    Config::set('lists.widgets', $widgetsData);

    $minutes = [];
    for($i = 0; $i < 16; $i += 1){
        $minutes[$i] = $i . ' ' . trans('front.minute_short');
    }
    for($i = 15; $i < 65; $i += 5){
        $minutes[$i] = $i . ' ' . trans('front.minute_short');
    }
    Config::set('tobuli.minutes', $minutes);
}

function getSelectTimeRange()
{
    $format = Appearance::getSetting('default_time_format') == 'h:i:s A' ? 'h:i A' : 'H:i';
    $date = Carbon::createMidnightDate();
    $times = [];
    for ($i = 0; $i < 96; $i++) {
        $times[$date->format('H:i')] = $date->format($format);
        $date->addMinutes(15);
    }

    return $times;
}

/**
 * Get js settings
 *
 * @return Array
 */
function getJsConfig()
{
    $user = getActingUser();
    $hash = request()->route()->parameter('hash');

    $checkUrl = $hash ? route('sharing.devices_latest', ['hash' => $hash]) : route('objects.items_json');
    $devicesUrl = $hash ? route('sharing.devices', ['hash' => $hash]) : route('objects.items');
    $addressUrl = $hash ? route('sharing.address', ['hash' => $hash]) : route('address.get');

    $googleQueryParam = [
        'key' => settings('main_settings.google_maps_key'),
        'language' => Language::iso()
    ];

    if (config('addon.google_styled')) {
        $googleQueryParam['region'] = 'MA';
    }

    return [
            'debug' => false,
            'user_id' => $user->id,
            'version' => config('tobuli.version'),
            'firstLogin' => $user && ! $user->isLoggedBefore(),
            'offlineTimeout' => settings('main_settings.default_object_online_timeout') * 60,
            'checkFrequency' => config('tobuli.check_frequency'),
            'checkChatFrequency' => config('tobuli.check_chat_frequency'),
            'checkChatUnreadFrequency' => config('tobuli.check_chat_unread_frequency'),
            'lang' => Language::get(),
            'show_object_info_after' => settings('plugins.show_object_info_after.status'),
            'object_listview' => settings('plugins.object_listview.status'),
            'channels' => [
                'userChannel' => md5('user_'.$user->id),
            ],
            'settings' => [
                'units' => [
                    'speed'    => [
                        'unit' => Formatter::speed()->getUnit(),
                        'radio' => Formatter::speed()->getRatio()
                    ],
                    'distance' => [
                        'unit' => Formatter::distance()->getUnit(),
                        'radio' => Formatter::distance()->getRatio()
                    ],
                    'altitude' => [
                        'unit' => Formatter::altitude()->getUnit(),
                        'radio' => Formatter::altitude()->getRatio()
                    ],
                    'capacity' => [
                        'unit' => Formatter::capacity()->getUnit(),
                        'radio' => Formatter::capacity()->getRatio()
                    ],
                ],
                'durationFormat' => Formatter::duration()->getFormat(),
                'timeFormat' => convertPHPToMomentFormat(Appearance::getSetting('default_time_format')),
                'dateFormat' => convertPHPToMomentFormat(Appearance::getSetting('default_date_format')),
                'weekStart' => $user->week_start_day,
                'mapCenter' => [
                    'lat' => floatval(Appearance::getSetting('map_center_latitude')),
                    'lng' => floatval(Appearance::getSetting('map_center_longitude'))
                ],
                'mapZoom' => Appearance::getSetting('map_zoom_level'),
                'map_id' => $user->map_id,
                'availableMaps' => $user->available_maps,
                'toggleSidebar' => false,
                'showTraffic' => false,
                'showTotalDistance' => settings('plugins.device_widget_total_distance.status'),
                'animateDeviceMove' =>  settings('plugins.device_move_animation.status'),
                'showGeofenceSize' => settings('plugins.geofence_size.status'),
                'showEventSectionAddress' => settings('plugins.event_section_address.status'),
                'showDevice' => $user->map_controls->m_objects ? true : false,
                'showGeofences' => $user->map_controls->m_geofences ? true : false,
                'showRoutes' => $user->map_controls->m_routes ? true : false,
                'showPoi' => $user->map_controls->m_poi ? true : false,
                'showTail' => $user->map_controls->m_show_tails ? true : false,
                'showNames' => $user->map_controls->m_show_names ? true : false,
                'showHistoryRoute' => $user->map_controls->history_control_route  ? true : false,
                'showHistoryArrow' => $user->map_controls->history_control_arrows ? true : false,
                'showHistoryStop' => $user->map_controls->history_control_stops  ? true : false,
                'showHistoryEvent' => $user->map_controls->history_control_events ? true : false,
                'keys' => [
                    'google_maps_key' => settings('main_settings.google_maps_key'),
                    'here_map_id' => settings('main_settings.here_map_id'),
                    'here_map_code' => settings('main_settings.here_map_code'),
                    'here_api_key' => settings('main_settings.here_api_key'),
                    'mapbox_access_token' => settings('main_settings.mapbox_access_token'),
                    'bing_maps_key' => settings('main_settings.bing_maps_key'),
                    'maptiler_key' => settings('main_settings.maptiler_key'),
                    'tomtom_key' => settings('main_settings.tomtom_key'),
                ],
                'googleQueryParam' => $googleQueryParam,
                'openmaptiles_url' => settings('main_settings.openmaptiles_url'),
                'showStreetView' => ( ! settings('main_settings.streetview_key') && (settings('main_settings.streetview_api') != 'default'))
                    ? false
                    : true,
            ],
            'urls' => [
                'asset' => asset(''),
                'streetView' => route('streetview'),
                'geoAddress' => $addressUrl,

                'events' => route('events.index'),
                'eventDoDelete' => route('events.do_destroy'),

                'history' => route('history.index'),
                'historyExport' => route('history.export'),
                'historyPosition' => route('history.position'),
                'historyPositions' => route('history.positions'),
                'historyPositionsDelete' => route('history.delete_positions'),

                'check' => $checkUrl,
                'devicesMap' => $devicesUrl,
                'devicesSidebar' => route('objects.sidebar'),
                'deviceDelete' => route('objects.destroy'),
                'deviceChangeActive' => route('devices.change_active'),
                'deviceToggleGroup' => route('objects.change_group_status'),
                'deviceStopTime' => route('objects.stop_time').'/',
                'deviceFollow' => route('devices.follow_map').'/',
                'devicesSensorCreate' => route('sensors.create').'/',
                'devicesServiceCreate' => route('services.create').'/',
                'devicesServices' => route('services.index').'/',
                'devicesCommands' => route('devices.commands'),
                'deviceImages' => route('device_media.get_images').'/',
                'deviceImage' => route('device_media.get_image').'/',
                'deleteImage' => route('device_media.delete_image').'/',
                'deviceSendGprsCommand' => route('send_command.gprs'),
                'deviceWidgetLocation' => route('device.widgets.location').'/',
                'deviceWidgetCameras' => route('device.widgets.cameras').'/',
                'deviceWidgetImage' => route('device.widgets.image').'/',
                'deviceWidgetUploadImage' => route('device.image_upload').'/',
                'deviceWidgetFuelGraph' => route('device.widgets.fuel_graph').'/',
                'deviceWidgetGprsCommand' => route('device.widgets.gprs_command').'/',
                'deviceWidgetRecentEvents' => route('device.widgets.recent_events').'/',
                'deviceWidgetTemplateWebhook' => route('device.widgets.template_webhook').'/',

                'geofencesMap' => route('geofences.index'),
                'geofencesSidebar' => route('geofences.sidebar'),
                'geofenceCreate' => route('geofences.create'),
                'geofenceEdit' => route('geofences.edit'),
                'geofenceChangeActive' => route('geofences.change_active'),
                'geofenceDelete' => route('geofences.destroy'),
                'geofencesExportType' => route('geofences.export_type'),
                'geofenceDevices' => route('geofences.devices'),
                'geofencesImport' => route('geofences.import'),
                'geofenceToggleGroup' => route('geofences_groups.change_status'),

                'routesSidebar' => route('routes.sidebar'),
                'routesMap' => route('routes.index'),
                'routesCreate' => route('routes.create'),
                'routesEdit' => route('routes.edit'),
                'routeChangeActive' => route('routes.change_active'),
                'routeDelete' => route('routes.destroy'),
                'routesExportType' => route('routes.export_type'),

                'alerts' => route('alerts.index'),
                'alertEdit' => route('alerts.edit'),
                'alertChangeActive' => route('alerts.change_active'),
                'alertDelete' => route('alerts.destroy'),
                'alertGetEvents' => route('custom_events.get_events'),
                'alertGetProtocols' => route('custom_events.get_protocols'),
                'alertGetEventsByDevice' => route('custom_events.get_events_by_device'),
                'alertGetCommands' => route('alerts.commands'),

                'poisMap' => route('pois.index'),
                'poisSidebar' => route('pois.sidebar'),
                'poisCreate' => route('pois.create'),
                'poisEdit' => route('pois.edit'),
                'poisDelete' => route('pois.destroy'),
                'poisChangeActive' => route('pois.change_active'),
                'poisToggleGroup' => route('pois_groups.change_status'),
                'poisExportType' => route('pois.export_type'),

                'changeMap' => route('my_account.change_map'),
                'changeMapSettings' => route('my_account_settings.change_map_settings'),

                'clearQueue' => route('sms_gateway.clear_queue'),

                'dashboard' => route('dashboard'),
                'dashboardBlockContent' => route('dashboard.block_content'),

                'lockHistory' => route('lock_status.history').'/',
                'lockStatus' => route('lock_status.status').'/',
                'unlockLock' => route('lock_status.unlock').'/',

                'checklistUpdateRowStatus' => route('checklists.update_row_status').'/',
                'checklistUpdateRowOutcome' => route('checklists.update_row_outcome').'/',
                'checklistUploadFile' => route('checklists.upload_file').'/',
                'checklistSign' => route('checklists.sign_checklist').'/',
                'checklistGetRow' => route('checklists.get_row').'/',

                'deviceConfigApnData' => route('device_config.get_apn_data').'/',

                'importGetFields' => route('import.get_fields'),

                'chatUnreadMsgTotalCount' => route('chat.unread_msg_count'),
            ],
        ];
}

/**
 * Set acting user
 *
 * @param User $user
 * @return void
 */
function setActingUser(User $user)
{
    App::instance('acting_user', $user);
    Appearance::resolveUser($user, true);
    Formatter::byUser($user);
}

/**
 * Get acting user
 *
 * @return User
 */
function getActingUser()
{
    return App::bound('acting_user') ? App::make('acting_user') : null;
}

/**
 * Get period by phrase
 *
 * @throws ValidationException
 */
function getPeriodByPhrase(string $phrase): array
{
    $endDate = \Carbon\Carbon::now()->endOfDay();
    $startDate = \Carbon\Carbon::now()->startOfDay();

    switch ($phrase) {
        case 'today':
            break;
        case 'yesterday':
            $endDate = $endDate->subDays(1);
            $startDate = $startDate->subDays(1);

            break;
        case 'this_week':
            $endDate = $endDate->endOfWeek();
            $startDate = $startDate->startOfWeek();

            break;
        case 'last_week':
            $endDate = $endDate->endOfWeek()->subWeeks(1);
            $startDate = $startDate->startOfWeek()->subWeeks(1);

            break;
        case 'this_month':
            $endDate = $endDate->endOfMonth();
            $startDate = $startDate->startOfMonth();

            break;
        case 'last_month':
            $endDate = $endDate->endOfMonth()->subMonths(1);
            $startDate = $startDate->startOfMonth()->subMonths(1);

            break;
        default:
            throw new \Tobuli\Exceptions\ValidationException([
                'phrase' => 'Unknown phrase: '.$phrase,
            ]);

            break;
    }

    return [
        'start' => $startDate,
        'end' => $endDate,
    ];
}

function getCompletionStatus($status = null)
{
    $statuses = [
        'all' => trans('front.all'),
        'complete' => trans('front.complete'),
        'incomplete' => trans('front.incomplete'),
        'failed' => trans('global.failed'),
    ];

    return $status ? $statuses[$status] : $statuses;
}

/**
 * Check if given string is base64 string
 *
 * @param String $string base64 string
 * @return boolean
 */
function isBase64($string)
{
    if (! is_string($string)) {
        return false;
    }

    if (strpos($string, ',') !== false) {
        $string = explode(',', $string);
        $string = $string[1];
    }

    return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string);
}

/**
 * Convert base64 string to UploadedFile object
 */
function base64ToImage(string $string): ?\Symfony\Component\HttpFoundation\File\UploadedFile
{
    if (strpos($string, ',') === false) {
        return null;
    }

    list($imageType, $imageData) = explode(',', $string);
    preg_match("/data:image\/(.*?);/", $imageType, $imageType);
    $imageType = $imageType[1];

    $name = uniqid('image_');
    $path = sys_get_temp_dir()."/{$name}.{$imageType}";
    file_put_contents($path, base64_decode($imageData));

    return new \Symfony\Component\HttpFoundation\File\UploadedFile(
        $path,
        "{$name}.{$imageType}",
        File::mimeType($path),
        File::size($path),
        null,
        true);
}

/**
 * Get available DST types with translations
 *
 * @return Array
 */
function getDSTTypes()
{
    return [
        'none' => trans('front.none'),
        'exact' => trans('front.exact_date'),
        'automatic' => trans('front.automatic'),
        'other' => trans('front.other')
    ];
}

/**
 * Get array of month with translations
 *
 * @return Array
 */
function getMonths()
{
    return [
        'january' => trans('front.january'),
        'february' => trans('front.february'),
        'march' => trans('front.march'),
        'april' => trans('front.april'),
        'may' => trans('front.may'),
        'june' => trans('front.june'),
        'july' => trans('front.july'),
        'august' => trans('front.august'),
        'september' => trans('front.september'),
        'october' => trans('front.october'),
        'november' => trans('front.november'),
        'december' => trans('front.december')
    ];
}

/**
 * Get array of weekdays with translations
 *
 * @return Array
 */
function getWeekdays()
{
    return [
        'monday' => trans('front.monday'),
        'tuesday' => trans('front.tuesday'),
        'wednesday' => trans('front.wednesday'),
        'thursday' => trans('front.thursday'),
        'friday' => trans('front.friday'),
        'saturday' => trans('front.saturday'),
        'sunday' => trans('front.sunday')
    ];
}

/**
 * Get array of available week positions with translations
 *
 * @return Array
 */
function getWeekPositions()
{
    return [
        'first' => trans('front.first'),
        'second' => trans('front.second'),
        'third'  => trans('front.third'),
        'last' => trans('front.last'),
    ];
}

/**
 * Get array of available week start days with translations
 *
 * @return Array
 */
function getWeekStartDays()
{
    return [
        '1' => trans('front.monday'),
        '0' => trans('front.sunday'),
        '6' => trans('front.saturday'),
        '5' => trans('front.friday'),
    ];
}

/**
 * Get available DST countries
 *
 * @return Array
 */
function getDSTCountries()
{
    return DB::table('timezones_dst')
        ->pluck('country', 'id')->all();
}

function filesize_remote($url) {
    static $regex = '/^Content-Length: *+\K\d++$/im';
    if (!$fp = @fopen($url, 'rb')) {
        return false;
    }
    if (
        isset($http_response_header) &&
        preg_match($regex, implode("\n", $http_response_header), $matches)
    ) {
        return (int)$matches[0];
    }
    return strlen(stream_get_contents($fp));
}

function arrayToHtmlInput(array $args, string $prefix = ''): string
{
    $html = '';

    foreach ($args as $key => $value) {
        $name = $prefix ? $prefix . '[' . $key . ']' : $key;

        if (is_array($value)) {
            $html .= arrayToHtmlInput($value, $name);
        } else {
            $html .= Form::hidden($name, $value);
        }
    }

    return $html;
}
