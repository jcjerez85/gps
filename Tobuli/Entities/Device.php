<?php

namespace Tobuli\Entities;

use App\Events\Device\DeviceDisabled;
use App\Events\Device\DeviceEnabled;
use App\Events\Device\DeviceSubscriptionActivate;
use App\Events\Device\DeviceSubscriptionRenew;
use App\Jobs\TrackerConfigWithRestart;
use Carbon\Carbon;
use CustomFacades\Repositories\TraccarDeviceRepo;
use Formatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\DatabaseService;
use Tobuli\Traits\AttributesRelationsGetter;
use Tobuli\Traits\Chattable;
use Tobuli\Traits\Customizable;
use Tobuli\Traits\EventLoggable;
use Tobuli\Traits\FcmTokensTrait;
use Tobuli\Traits\Filterable;
use Tobuli\Traits\Includable;
use Tobuli\Traits\Nameable;
use Tobuli\Traits\Searchable;
use Tobuli\Utils\NetworkUtil;

class Device extends AbstractEntity implements FcmTokenableInterface, ChattableInterface
{
    use Chattable, EventLoggable, Searchable, Filterable, Includable, Nameable, Customizable, FcmTokensTrait, AttributesRelationsGetter;


    const STATUS_ACK     = 'ack';
    const STATUS_OFFLINE = 'offline';
    const STATUS_ONLINE  = 'online';
    const STATUS_ENGINE  = 'engine';

    const IMAGE_PATH     = 'images/device_images/';

    const KIND_GENERAL = 0;
    const KIND_BEACON = 1;

    protected $table = 'devices';

    protected $fillable = array(
        'deleted',
        'traccar_device_id',
        'timezone_id',
        'name',
        'imei',
        'icon_id',
        'fuel_measurement_id',
        'fuel_quantity',
        'fuel_price',
        'fuel_per_km',
        'sim_number',
        'device_model',
        'plate_number',
        'vin',
        'registration_number',
        'object_owner',
        'additional_notes',
        'comment',
        'expiration_date',
        'tail_color',
        'tail_length',
        'engine_hours',
        'detect_engine',
        'detect_distance',
        'detect_speed',
        'min_moving_speed',
        'min_fuel_fillings',
        'min_fuel_thefts',
        'snap_to_road',
        'gprs_templates_only',
        'valid_by_avg_speed',
        'icon_colors',
        'parameters',
        'currents',
        'active',
        'forward',
        'sim_activation_date',
        'sim_expiration_date',
        'installation_date',
        'msisdn',
        'device_type_id',
        'app_tracker_login',
        'fuel_detect_sec_after_stop',
        'authentication'
    );

    protected $appends = [
        //'stop_duration'
        //'lat',
        //'lng',
        //'speed',
        //'course',
        //'altitude',
        //'protocol',
        //'time'
    ];

    //protected $hidden = ['currents'];

    protected $casts = [
        'currents' => 'array'
    ];

    protected $searchable = [
        'name',
        'imei',
        'sim_number',
        'vin',
        'plate_number',
        'registration_number',
        'object_owner',
        'device_model',
        'additional_notes'
    ];
    protected $filterables = [
        'id',
        'imei',
        'sim_number',
        'group_id'
    ];

    protected $hidden = ['app_uuid'];

    private $attributesRelations = [
        'users_emails' => ['users'],
        'stop_duration' => ['traccar'],
        'speed' => ['traccar'],
        'status' => ['traccar'],
        'status_color' => ['traccar'],
        'protocol' => ['traccar'],
        'time' => ['traccar'],
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($device) {
            $traccar_item = TraccarDeviceRepo::create([
                'name' => $device->name,
                'uniqueId' => $device->imei,
                'database_id' => DatabaseService::instance()->getActiveDatabaseId()
            ]);

            $device->traccar_device_id = $traccar_item->id;
        });

        static::updated(function ($device) {
            if ($device->isDirty('name') || $device->isDirty('imei')) {
                TraccarDeviceRepo::update($device->traccar_device_id, [
                    'name' => $device->name,
                    'uniqueId' => $device->imei
                ]);
            }
        });

        static::saving(function (Device $device) {
            if ($device->isDirty('forward')) {
                $hosts = empty($device->forward['ip']) ? [] : semicol_explode($device->forward['ip']);

                foreach ($hosts as $host) {
                    if (NetworkUtil::isHostSelfReferencing($host)) {
                        throw new ValidationException($host . ' host is referring to server IP');
                    }
                }
            }

            if ($device->isDirty('fuel_measurement_id') || $device->isDirty('fuel_quantity')) {
                if ($device->fuel_measurement_id == 4) {
                    $device->fuel_per_km = 0;
                    $device->fuel_per_h = convertFuelConsumption($device->fuel_measurement_id, $device->fuel_quantity);
                } else {
                    $device->fuel_per_km = convertFuelConsumption($device->fuel_measurement_id, $device->fuel_quantity);
                    $device->fuel_per_h = 0;
                }
            }
        });

        static::saved(function ($device) {
            if ($device->isDirty('imei'))
                UnregisteredDevice::where('imei', $device->imei)->delete();

            if ($device->isDirty('forward'))
                dispatch((new TrackerConfigWithRestart()));

            if ($device->isDirty('active')) {
                event($device->active ? new DeviceEnabled($device) : new DeviceDisabled($device));
            }
        });
    }

    public function positions()
    {
        return $this->traccar->positions();
    }

    public function positionTraccar()
    {
        if ( ! $this->traccar) {
            return null;
        }

        return new \Tobuli\Entities\TraccarPosition([
            'id' => $this->traccar->lastestPosition_id,
            'device_id' => $this->traccar->id,
            'latitude' => $this->traccar->lastValidLatitude,
            'longitude' => $this->traccar->lastValidLongitude,
            'other' => $this->traccar->other,
            'speed' => $this->traccar->speed,
            'altitude' => $this->traccar->altitude,
            'course' => $this->traccar->course,
            'time' => $this->traccar->time,
            'device_time' => $this->traccar->device_time,
            'server_time' => $this->traccar->server_time,
            'protocol' => $this->traccar->protocol,
            'valid' => true
        ]);
    }

    public function createPositionsTable()
    {
        $connection = $this->positions()->getRelated()->getConnectionName();
        $tableName = $this->positions()->getRelated()->getTable();

        if (Schema::connection($connection)->hasTable($tableName))
            Schema::connection($connection)->dropIfExists($tableName);

        Schema::connection($connection)->create($tableName, function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->double('altitude')->nullable();
            $table->double('course')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->text('other')->nullable();
            $table->double('speed')->nullable()->index();
            $table->datetime('time')->nullable()->index();
            $table->datetime('device_time')->nullable();
            $table->datetime('server_time')->nullable()->index();
            $table->text('sensors_values')->nullable();
            $table->tinyInteger('valid')->nullable();
            $table->double('distance')->nullable();
            $table->string('protocol', 20)->nullable();
        });
    }

    public function icon()
    {
        //return $this->hasOne('Tobuli\Entities\DeviceIcon', 'id', 'icon_id');
        return $this->hasOne('Tobuli\Entities\DeviceIcon', 'id', 'icon_id');
    
    }

    public function getIconAttribute()
    {
        $icon = $this->getRelationValue('icon');

        return $icon ? $icon->setStatus($this->getStatus()) : null;
    }

    public function traccar()
    {
        return $this->hasOne('Tobuli\Entities\TraccarDevice', 'id', 'traccar_device_id');
    }

    public function alerts()
    {
        return $this->belongsToMany('Tobuli\Entities\Alert', 'alert_device', 'device_id', 'alert_id')
            // escape deattached users devices
            ->join('user_device_pivot', function ($join) {
                $join
                    ->on('user_device_pivot.device_id', '=', 'alert_device.device_id')
                    ->on('user_device_pivot.user_id', '=', 'alerts.user_id');
            })
            ->withPivot('started_at', 'fired_at', 'active_from', 'active_to');
    }

    public function events()
    {
        return $this->hasMany('Tobuli\Entities\Event', 'device_id');
    }

    public function last_event()
    {
        $query = $this->hasOne('Tobuli\Entities\Event', 'device_id');

        if ($user = getActingUser()) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('id', 'desc');
    }

    public function users() {
        return $this->belongsToMany('Tobuli\Entities\User', 'user_device_pivot', 'device_id', 'user_id')->withPivot('group_id', 'current_driver_id', 'current_events');
    }

    public function driver() {
        //return $this->belongsToMany('Tobuli\Entities\UserDriver', 'user_device_pivot', 'device_id', 'current_driver_id');
        return $this->hasOne('Tobuli\Entities\UserDriver', 'id', 'current_driver_id');
    }

    public function drivers() {
        return $this->hasMany('Tobuli\Entities\UserDriver', 'device_id');
    }

    public function sensors() {
        return $this->hasMany('Tobuli\Entities\DeviceSensor', 'device_id');
    }

    public function services() {
        return $this->hasMany('Tobuli\Entities\DeviceService', 'device_id');
    }

    public function expenses()
    {
        return $this->hasMany('Tobuli\Entities\DeviceExpense', 'device_id');
    }

    public function timezone()
    {
        return $this->hasOne('Tobuli\Entities\Timezone', 'id', 'timezone_id');
    }

    public function deviceCameras() {
        return $this->hasMany('Tobuli\Entities\DeviceCamera', 'device_id');
    }

    public function group()
    {
        return $this->hasOne('Tobuli\Entities\DeviceGroup', 'id', 'group_id');
    }

    public function plans()
    {
        return $this->belongsToMany('Tobuli\Entities\DevicePlan', 'device_device_plan', 'device_id', 'plan_id');
    }

    public function deviceType()
    {
        return $this->hasOne('Tobuli\Entities\DeviceType', 'id', 'device_type_id');
    }

    public function subscriptions()
    {
        return $this->hasManyThrough(
            'Tobuli\Entities\Subscription',
            'Tobuli\Entities\Order',
            'entity_id',
            'order_id',
            'id',
            'id'
        )->where('orders.entity_type', 'device')->active();
    }

    public function sentCommands()
    {
        return $this->belongsTo(SentCommand::class, 'imei', 'device_imei');
    }

    public function setDeviceTypeIdAttribute($value)
    {
        $this->attributes['device_type_id'] = empty($value) ? null : $value;
    }

    public function setTimezoneIdAttribute($value)
    {
        $this->attributes['timezone_id'] = empty($value) ? null : $value;
    }

    public function setIconColorsAttribute($value)
    {
        $this->attributes['icon_colors'] = json_encode($value);
    }

    public function getIconColorsAttribute($value)
    {
        return json_decode($value, TRUE);
    }

    public function setForwardAttribute($value)
    {
        if (Arr::get($value, 'active'))
            $this->attributes['forward'] = json_encode($value);
        else
            $this->attributes['forward'] = null;
    }

    public function getForwardAttribute($value)
    {
        return json_decode($value, TRUE);
    }

    public function isSimExpired()
    {
        if ( ! $this->hasSimExpireDate())
            return false;

        return  strtotime($this->sim_expiration_date) < time();
    }

    public function hasSimExpireDate()
    {
        if ( ! $this->sim_expiration_date)
            return false;

        if ($this->sim_expiration_date == '0000-00-00')
            return false;

        return true;
    }

    public function isExpiredWithoutExtra()
    {
        if ( ! $this->hasExpireDate())
            return false;

        return  strtotime($this->expiration_date) < time();
    }

    public function isExpired(): bool
    {
        if (!$this->isExpiredWithoutExtra()) {
            return false;
        }

        $extraTime = settings('main_settings.extra_expiration_time');

        return !$extraTime
            || \Carbon::parse($this->expiration_date)->addSeconds($extraTime)->getTimestamp() <= time();
    }

    public function hasExpireDate()
    {
        if ( ! $this->expiration_date)
            return false;

        if ($this->expiration_date == '0000-00-00')
            return false;

        if ($this->expiration_date == '0000-00-00 00:00:00')
            return false;

        return true;
    }

    public function isPlanAble()
    {
        if (!settings('main_settings.enable_device_plans'))
            return false;

        if (!$this->hasExpireDate())
            return false;

        return true;
    }

    public function isConnected()
    {
        return Redis::connection('process')->get('connected.' . $this->imei) ? true : false;
    }

    public function getParameter($key, $default = null)
    {
        $parameters = $this->getParameters();

        return array_key_exists($key, $parameters) ? $parameters[$key] : $default;
    }

    public function setParameter($key, $value)
    {
        $parameters = $this->getParameters();

        $parameters[$key] = $value;

        $this->setParameters($parameters);
    }

    public function setParameters($value)
    {
        if ( is_array($value))
        {
            $xml = '<info>';

            foreach ($value as $key => $val)
            {
                if (is_numeric($key)) continue;
                if (is_array($val)) continue;

                $val = is_bool($val) ? ($val ? 'true' : 'false') : $val;
                $val = html_entity_decode($val);
                $xml .= "<{$key}>{$val}</$key>";
            }
            $xml .= '</info>';

            $value = $xml;
        }

        $this->traccar->other = $value;
    }

    public function getParameters()
    {
        if ( ! isset($this->traccar->other))
            return [];

        $parameters = parseXMLToArray($this->traccar->other);

        return $parameters;
    }

    public function getSumDistance($from = null, $to = null)
    {
        $query = $this->positions();

        if ($from)
            $query->where('time', '>', $from);

        if ($to)
            $query->where('time', '<', $to);

        return $query->sum('distance');
    }

    public function getTotalDistance()
    {
        $distance = $this->getParameter('totaldistance') / 1000;

        return Formatter::distance()->format($distance);
    }

    public function getSpeed($position = null) {
        $speed = 0;

        if (is_null($position) && $this->getTimeoutStatus() != self::STATUS_ONLINE)
            return $speed;

        $sensor = $this->getSpeedSensor();

        if ($sensor) {
            $other = is_null($position) ? ($this->traccar->other ?? null) : ($position->other ?? null);
            $speed = $sensor->getValue($other, false);
        } else {
            $speed = is_null($position) ? ($this->traccar->speed ?? null) : ($position->speed ?? null);
        }

        return $speed;
    }

    public function getTimeoutStatus()
    {
        static $minutes = null;

        if (is_null($minutes))
            $minutes = settings('main_settings.default_object_online_timeout') * 60;

        $ackTime = strtotime($this->getAckTime());
        $serverTime = strtotime($this->getServerTime());

        if ($serverTime < time() - $minutes)
            return self::STATUS_OFFLINE;

        return $serverTime > $ackTime ? self::STATUS_ONLINE : self::STATUS_ACK;
    }

    public function getStatusAttribute() {
        return $this->getStatus();
    }

    public function getStatus()
    {
        if (!$this->active)
            return self::STATUS_OFFLINE;

        if ($this->isExpired())
            return self::STATUS_OFFLINE;

        $status = $this->getTimeoutStatus();

        if ($status != self::STATUS_ONLINE)
            return $status;

        $speed = $this->getSpeed();

        if ($speed >= $this->min_moving_speed)
            return self::STATUS_ONLINE;

        $stopDuration = $this->getStopDuration();

        if (is_null($stopDuration))
            return self::STATUS_ACK;

        if ($stopDuration < 10)
            return self::STATUS_ONLINE;

        if (null !== $engine = $this->getEngineStatus()) {
            return $engine ? self::STATUS_ENGINE : self::STATUS_ACK;
        }

        return self::STATUS_ACK;
    }

    public function getStatusColorAttribute() {
        return $this->getStatusColor();
    }

    public function getStatusColor($status = null)
    {
        if (is_null($status)) {
            $status = $this->getStatus();
        }

        switch ($status) {
            case 'online':
                $icon_status = 'moving';
                break;
            case 'ack':
                $icon_status = 'stopped';
                break;
            case 'engine':
                $icon_status = 'engine';
                break;
            default:
                $icon_status = 'offline';
        }

        return Arr::get($this->icon_colors, $icon_status, 'red');
    }

    /**
     * @param array|string $type
     */
    public function getSensorsByType($type)
    {
        if (empty($this->sensors))
            return null;

        if (!is_array($type)) {
            $type = (array)$type;
        }

        return $this->sensors->filter(function ($sensor) use ($type) {
            return in_array($sensor->type, $type);
        });
    }

    public function getSensorByType($type)
    {
        $sensors = $this->sensors;

        if (empty($sensors))
            return null;

        foreach ($sensors as $sensor) {
            if ($sensor['type'] == $type) {
                $type_sensor = $sensor;
                break;
            }
        }

        if (empty($type_sensor))
            return null;

        return $type_sensor;
    }

    public function getRfidSensor()
    {
        return $this->getSensorByType('rfid');
    }

    public function getFuelTankSensor()
    {
        $sensor = $this->getSensorByType('fuel_tank');

        if ($sensor)
            return $sensor;

        return $this->getSensorByType('fuel_tank_calibration');
    }

    public function getLoadSensor()
    {
        $sensor = $this->getSensorByType('load');

        if ($sensor) {
            return $sensor;
        }

        return $this->getSensorByType('load_calibration');
    }

    public function getOdometerSensor()
    {
        return $this->getSensorByType('odometer');
    }

    public function getSpeedSensor()
    {
        $detect_speed = $this->detect_speed ?? null;

        if ($detect_speed != 'speed_ecm')
            return null;

        return $this->getSensorByType('speed_ecm');
    }

    public function getEngineHoursSensor()
    {
        return $this->getSensorByType('engine_hours');
    }

    public function getEngineSensor()
    {
        $detect_engine = $this->engine_hours == 'engine_hours' ? $this->detect_engine : $this->engine_hours;

        if (empty($detect_engine))
            return null;

        if ($detect_engine == 'gps')
            return null;

        return $this->getSensorByType($detect_engine);
    }

    public function getEngineStatusAttribute()
    {
        return $this->getEngineStatus();
    }

    public function getEngineStatus($formated = false)
    {
        $sensor = $this->getEngineSensor();

        if (empty($sensor))
            return $formated ? '-' : null;

        if ($this->getTimeoutStatus() == self::STATUS_OFFLINE)
            return false;

        $value = $sensor->getValueCurrent($this->other);

        return $formated ? $sensor->formatValue($value) : (bool)$value;
    }

    public function getDistanceBetween($dateFrom, $dateTo)
    {
        $odometer = $this->getOdometerSensor();

        $query = $this->positions()->whereBetween('time', [$dateFrom, $dateTo])->limit(1);

        if ( ! is_null($odometer) && $odometer->odometer_value_by != 'virtual_odometer') {
            $query->where('sensors_values', 'like', '%'.$odometer->id.'%');
        }

        $first     = (clone $query)->orderBy('time', 'asc');
        $positions = (clone $query)->orderBy('time', 'desc')->union($first)->get();

        if ($positions->count() < 2)
            return 0;

        if ( ! is_null($odometer) && $odometer->odometer_value_by != 'virtual_odometer') {
            $to = (float)$odometer->getValuePosition($positions[0]);
            $from = (float)$odometer->getValuePosition($positions[1]);

            $distance = (empty($from) || empty($to)) ? 0 : $to - $from;
        } else {
            $distance = $positions[0]->getParameter('totaldistance') - $positions[1]->getParameter('totaldistance');

            //from meters to kilometers
            $distance = $distance / 1000;
        }

        return ($distance > 0) ? $distance : 0;
    }

    public function setProtocolAttribute($value)
    {
        $this->attributes['protocol'] = $value;
    }

    public function getProtocolAttribute()
    {
        return isset($this->traccar->protocol) ? $this->traccar->protocol : null;
    }

    public function getDeviceTime()
    {
        return $this->traccar && $this->traccar->device_time ? $this->traccar->device_time : null;
    }

    public function getTime()
    {
        return $this->traccar && $this->traccar->time ? $this->traccar->time : null;
    }

    public function getAckTime()
    {
        return $this->traccar && $this->traccar->ack_time ? $this->traccar->ack_time : '';
    }

    public function getServerTime()
    {
        return $this->traccar && $this->traccar->server_time ? $this->traccar->server_time : '';
    }

    public function getTimeAttribute()
    {
        if (!$this->active)
            return trans('front.disabled');

        if (settings('plugins.display_sim_expired.status') && $this->isSimExpired())
            return trans('front.sim_expired');

        if ($this->isExpired())
            return trans('front.expired');

        $time = max($this->getTime(), $this->getAckTime());
        $timeFormat = Formatter::time()->human($time);

        if (empty($time) || substr($time, 0, 4) == '0000')
            return trans('front.not_connected');

        return $timeFormat;
    }
    
    public function getTimeTextAttribute()
    {
        if (!config('addon.time_text'))
            return NULL;

        if (!$this->active)
            return NULL;

        if (settings('plugins.display_sim_expired.status') && $this->isSimExpired())
            return NULL;

        if ($this->isExpired())
            return NULL;

        $time = max($this->getTime(), $this->getAckTime());
        $timeFormat = Formatter::time()->human($time);

        if (empty($time) || substr($time, 0, 4) == '0000')
            return NULL;

        if ($this->isMove())
            return trans('front.moving').' '.$this->moved_duration;
            
        if ($this->isIdle())
            return trans('front.idle').' '.$this->idle_duration;

        if ($this->isStop())
            return trans('front.stopped').' '.$this->stop_duration;

        if ($this->isOffline())
            return trans('front.offline').' '.$this->stop_duration;

        return $timeFormat;
    }

    public function getOnlineAttribute() {
        return $this->getStatus();
    }

    public function getLatAttribute()
    {
        if ($this->isExpired())
            return null;

        return isset($this->traccar->lastValidLatitude) ? cord($this->traccar->lastValidLatitude) : null;
    }

    public function getLngAttribute()
    {
        if ($this->isExpired())
            return null;

        return isset($this->traccar->lastValidLongitude) ? cord($this->traccar->lastValidLongitude) : null;
    }

    public function getLatitudeAttribute()
    {
        return isset($this->traccar->lastValidLatitude) ? cord($this->traccar->lastValidLatitude) : null;
    }

    public function getLongitudeAttribute()
    {
        return isset($this->traccar->lastValidLongitude) ? cord($this->traccar->lastValidLongitude) : null;
    }

    public function getCourseAttribute() {
        $course = 0;

        if (isset($this->traccar->course))
            $course = $this->traccar->course;

        return round($course);
    }

    public function getAltitudeAttribute() {
        $altitude = 0;

        if (isset($this->traccar->altitude))
            $altitude = $this->traccar->altitude;

        return Formatter::altitude()->format($altitude);
    }

    public function getTailAttribute() {
        $length = $this->tail_length;

        if (!$length)
            return [];

        if (empty($this->traccar->latest_positions))
            return [];

        $tail = [];
        $arr = explode(';',  $this->traccar->latest_positions);

        foreach ($arr as $value) {
            if ($length-- < 0)
                break;

            try {
                list($lat, $lng) = explode('/', $value);

                array_unshift($tail, [
                    'lat' => $lat,
                    'lng' => $lng
                ]);
            } catch (\Exception $e) {}
        }

        return $tail;
    }

    public function getLatestPositionsAttribute() {
        return isset($this->traccar->latest_positions) ? $this->traccar->latest_positions : null;
    }

    public function getTimestampAttribute() {
        if ($this->isExpired())
            return 0;

        return isset($this->traccar->server_time) ? strtotime($this->traccar->server_time) : 0;
    }

    public function getServerTimestampAttribute() {
        if ($this->isExpired())
            return 0;

        return isset($this->traccar->server_time) ? strtotime($this->traccar->server_time) : 0;
    }

    public function getAckTimestampAttribute() {
        if ($this->isExpired())
            return 0;

        return isset($this->traccar->ack_time) ? strtotime($this->traccar->ack_time) : 0;
    }

    public function getAckTimeAttribute() {
        if ($this->isExpired())
            return null;

        return isset($this->traccar->ack_time) ? $this->traccar->ack_time : null;
    }

    public function getServerTimeAttribute() {
        if ($this->isExpired())
            return null;

        return isset($this->traccar->server_time) ? $this->traccar->server_time : null;
    }

    public function getMovedAtAttribute() {
        if ($this->isExpired())
            return null;

        return isset($this->traccar->moved_at) ? $this->traccar->moved_at : null;
    }

    public function getMovedTimestampAttribute() {
        return $this->moved_at ? strtotime($this->moved_at) : 0;
    }

    public function getLastConnectTimeAttribute() {
        $lastConnect = $this->getLastConnectTimestampAttribute();

        return $lastConnect ? date('Y-m-d H:i:s', $lastConnect) : null;
    }

    public function getLastConnectTimestampAttribute() {
        return max($this->server_timestamp, $this->ack_timestamp);
    }

    public function getOtherAttribute() {
        return isset($this->traccar->other) ? $this->traccar->other : null;
    }

    public function getSpeedAttribute() {
        return Formatter::speed()->format($this->getSpeed());
    }

    public function getIdleDuration()
    {
        $engine_off_at = isset($this->traccar->engine_off_at) ? strtotime($this->traccar->engine_off_at) : 0;
        $engine_on_at  = isset($this->traccar->engine_on_at) ? strtotime($this->traccar->engine_on_at) : 0;
        $moved_at      = isset($this->traccar->moved_at) ? strtotime($this->traccar->moved_at) : 0;
        $time          = isset($this->traccar->time) ? strtotime($this->traccar->time) : 0;
        $server_time   = isset($this->traccar->server_time) ? strtotime($this->traccar->server_time) : 0;
        $engine_changed_at = isset($this->traccar->engine_changed_at) ? strtotime($this->traccar->engine_changed_at) : 0;

        if ( ! $moved_at)
            return 0;

        if ( ! $engine_off_at)
            return 0;

        if ($engine_on_at < $engine_off_at)
            return 0;

        $check_at = max($engine_changed_at, $moved_at);

        //device send incorrcet self timestamp
        if ($server_time > $time)
            return time() - $check_at + ($time - $server_time);

        return time() - $check_at;
    }

    public function getIdleDurationAttribute()
    {
        $duration = $this->getIdleDuration();

        return Formatter::duration()->human($duration);
    }

    public function getIgnitionDuration()
    {
        $engineOn      = isset($this->traccar->engine_on_at) ? strtotime($this->traccar->engine_on_at) : 0;
        $engineChanged = isset($this->traccar->engine_changed_at) ? strtotime($this->traccar->engine_changed_at) : 0;
        $time          = isset($this->traccar->time) ? strtotime($this->traccar->time) : 0;
        $serverTime    = isset($this->traccar->server_time) ? strtotime($this->traccar->server_time) : 0;

        if (! $engineOn || ! $engineChanged) {
            return 0;
        }

        if ($engineChanged >= $engineOn) {
            return 0;
        }

        //device sent incorrcet self timestamp
        if ($serverTime > $time) {
            return time() - $engineChanged + ($time - $serverTime);
        }

        return time() - $engineChanged;
    }

    public function getIgnitionDurationAttribute()
    {
        $duration = $this->getIgnitionDuration();

        return Formatter::duration()->human($duration);
    }

    public function getStopDuration()
    {
        $moved_at    = isset($this->traccar->moved_at) ? strtotime($this->traccar->moved_at) : 0;
        $time        = isset($this->traccar->time) ? strtotime($this->traccar->time) : 0;
        $server_time = isset($this->traccar->server_time) ? strtotime($this->traccar->server_time) : 0;
        $ack_time    = isset($this->traccar->ack_time) ? strtotime($this->traccar->ack_time) : 0;

        if ( ! $moved_at)
            return null;

        $last_time = max($server_time, $ack_time);

        if ($time > $last_time )
            return time() - $moved_at + ($time - $last_time);

        //device send incorrect self timestamp
        if ($time > $server_time )
            return time() - $moved_at + ($time - $server_time);

        return time() - $moved_at;
    }

    public function getStopDurationAttribute()
    {
        $duration = $this->getStopDuration();

        if (is_null($duration))
            return '-';

        if ($duration < 5)
            $duration = 0;

        return Formatter::duration()->human($duration);
    }
    
    public function getMovedDuration()
    {
        $engine_on_at = isset($this->traccar->engine_on_at) ? strtotime($this->traccar->engine_on_at) : NULL;
        $engine_changed_at = isset($this->traccar->engine_changed_at) ? strtotime($this->traccar->engine_changed_at) : NULL;
        $moved_at = isset($this->traccar->moved_at) ? strtotime($this->traccar->moved_at) : NULL;

        if ( ! $moved_at)
            return null;

        $moved_durationArray = array_filter([$engine_on_at, $engine_changed_at, $moved_at]);
        if (empty($moved_durationArray)) 
            return null;
            
        $moved_duration = min($moved_durationArray);

        return abs(time() - $moved_duration);
    }

    public function getMovedDurationAttribute()
    {
        $duration = $this->getMovedDuration();

        return Formatter::duration()->human($duration);
    }


    public function getFormatSensors()
    {
        if ($this->isExpired())
            return null;

        $result = [];

        foreach ($this->sensors as $sensor) {
            if (in_array($sensor->type, ['harsh_acceleration', 'harsh_breaking', 'harsh_turning']))
                continue;

            $value = $sensor->getValueCurrent($this->other);

            $result[] = [
                'id'            => $sensor->id,
                'type'          => $sensor->type,
                'name'          => $sensor->formatName(),
                'show_in_popup' => $sensor->show_in_popup,

                //'text'          => htmlentities( $sensor->formatValue($value) ),
                'value'         => htmlspecialchars($sensor->formatValue($value)),
                'val'           => $value,
                'scale_value'   => $sensor->getValueScale($value)
            ];
        }

        return $result;
    }

    public function getFormatServices()
    {
        if ($this->isExpired())
            return null;

        $result = [];

        foreach ($this->services as $service)
        {
            $service->setSensors($this->sensors);

            $result[] = [
                'id'       => $service->id,
                'name'     => $service->name,
                'value'    => $service->expiration(),
                'expiring' => $service->isExpiring()
            ];
        }

        return $result;
    }

    public function generateTail() {
        $limit = 15;

        $positions = $this->positions()
            ->where('distance', '>', 0.02)
            ->orderliness()
            ->limit($limit)
            ->get();

        $tail_positions = [];

        foreach ($positions as $position) {
            $tail_positions[] = $position->latitude.'/'.$position->longitude;
        }

        $this->traccar->update([
            'latest_positions' => implode(';', $tail_positions)
        ]);
    }

    public function applyPositionsTimezone()
    {
        if ( ! $this->timezone ) {
            $value = 'device_time';
        } elseif ( $this->timezone->id == 57) {
            $value = 'device_time';
        } else {
            list($hours, $minutes) = explode(' ', $this->timezone->time);

            if ($this->timezone->prefix == 'plus')
                $value = "DATE_ADD(device_time, INTERVAL '$hours:$minutes' HOUR_MINUTE)";
            else
                $value = "DATE_SUB(device_time, INTERVAL '$hours:$minutes' HOUR_MINUTE)";
        }

        $this->traccar()->update(['time' => DB::raw($value)]);
        $this->positions()->update(['time' => DB::raw($value)]);
    }

    public function isCorrectUTC()
    {
        $change = 900; //15 mins

        $server_time = strtotime( $this->getServerTime() );
        $device_time = strtotime( $this->getDeviceTime() );

        if ($server_time && (abs($server_time - $device_time) < $change))
            return true;

        return false;
    }

    public function canChat()
    {
        if ($this->app_tracker_login) {
            return true;
        }

        return $this->protocol == 'osmand';
    }

    public function scopeFilterGroupId($query, $group_id)
    {
        $query->where('user_device_pivot.group_id', $group_id ?: 0);
    }

    public function scopeVisible($query)
    {
        $query->where('user_device_pivot.active', 1);
    }

    public function scopeNPerGroup($query, $group, $n = 10)
    {
        // queried table
        $table = ($this->getTable());

        // initialize MySQL variables inline
        $query->from( DB::raw("(SELECT @rank:=0, @group:=0) as vars, {$table}") );

        // if no columns already selected, let's select *
        if ( ! $query->getQuery()->columns)
        {
            $query->select("{$table}.*");
        }

        // make sure column aliases are unique
        $groupAlias = 'group_'.md5(time());
        $rankAlias  = 'rank_'.md5(time());

        // apply mysql variables
        $query->addSelect(DB::raw(
            "@rank := IF(@group = {$group}, @rank+1, 1) as {$rankAlias}, @group := {$group} as {$groupAlias}"
        ));

        // make sure first order clause is the group order
        $query->getQuery()->orders = (array) $query->getQuery()->orders;
        array_unshift($query->getQuery()->orders, ['column' => $group, 'direction' => 'asc']);

        // prepare subquery
        $subQuery = $query->toSql();

        // prepare new main base Query\Builder
        $newBase = $this->newQuery()
            ->from(DB::raw("({$subQuery}) as {$table}"))
            ->mergeBindings($query->getQuery())
            ->where($rankAlias, '<=', $n)
            ->getQuery();

        // replace underlying builder to get rid of previous clauses
        $query->setQuery($newBase);
    }

    public function changeDriver($driver, $time = null, $withoutAlerts = false)
    {
        if (is_null($time))
            $time = date('Y-m-d H:i:s');

        $this->current_driver_id = $driver->id ?? null;
        $this->save();

        DB::table('user_driver_position_pivot')->insert([
            'device_id' => $this->id,
            'driver_id' => $driver->id ?? null,
            'date'      => $time
        ]);

        if (!$driver || $withoutAlerts)
            return;

        $position = $this->positionTraccar();

        if (is_null($position))
            return;

        $alerts = $this->alerts->filter(function($item){
            return $item->type == 'driver';
        });

        foreach ($alerts as $alert) {
            $event = $this->events()->create([
                'type'         => 'driver',
                'user_id'      => $alert->user_id,
                'alert_id'     => $alert->id,
                'device_id'    => $this->id,
                'geofence_id'  => null,
                'position_id'  => $position->id,
                'altitude'     => $position->altitude,
                'course'       => $position->course,
                'latitude'     => $position->latitude,
                'longitude'    => $position->longitude,
                'speed'        => $position->speed,
                'time'         => $position->time,
                'message'      => $driver->name,
                'additional'   => [
                    'driver_id'   => $driver->id,
                    'driver_name' => $driver->name
                ]
            ]);

            SendQueue::create([
                'user_id'   => $event->user_id,
                'type'      => $event->type,
                'data'      => $event,
                'channels'  => $alert->channels
            ]);
        }
    }

    public function beacons(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'device_current_beacons_pivot', 'device_id', 'beacon_id');
    }

    public function setExpirationDateAttribute($value)
    {
        $this->attributes['expiration_date'] = is_null($value) ? '0000-00-00 00:00:00' : $value;
    }

    public function getExpirationDateAttribute($value)
    {
        if ($value == '0000-00-00') {
            return null;
        }

        if ($value == '0000-00-00 00:00:00') {
            return null;
        }

        return $value;
    }

    public function scopeChatable($query)
    {
        //need join outside where closure
        $query->traccarJoin();

        return $query->where(function($q) {
            $q->protocol('osmand');
            $q->orWhere('app_tracker_login', 1);
        });
    }

    public function scopeHasExpiration($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('expiration_date');
            $q->where('expiration_date', '!=', '0000-00-00');
            $q->where('expiration_date', '!=', '0000-00-00 00:00:00');
        });
    }

    public function scopeHasntExpiration($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expiration_date');
            $q->orWhere('expiration_date', '=', '0000-00-00');
            $q->orWhere('expiration_date', '=', '0000-00-00 00:00:00');
        });
    }

    public function scopeIsExpiringAfter($query, int $days, bool $addExtra = true)
    {
        $dateFrom = Carbon::now();
        $dateTo = Carbon::now()->addDays($days);

        if ($addExtra && ($extraTime = settings('main_settings.extra_expiration_time'))) {
            $dateFrom->subSeconds($extraTime);
            $dateTo->subSeconds($extraTime);
        }

        return $query
            ->hasExpiration()
            ->where('expiration_date', '>=', $dateFrom)
            ->where('expiration_date', '<=', $dateTo);
    }

    public function scopeIsExpiredBefore($query, int $days, bool $addExtra = true)
    {
        $date = Carbon::now()->subDays($days);

        if ($addExtra && ($extraTime = settings('main_settings.extra_expiration_time'))) {
            $date->subSeconds($extraTime);
        }

        return $query
            ->hasExpiration()
            ->where('expiration_date', '<=', $date);
    }

    public function scopeExpired($query, bool $addExtra = true)
    {
        $date = Carbon::now();

        if ($addExtra && ($extraTime = settings('main_settings.extra_expiration_time'))) {
            $date->subSeconds($extraTime);
        }

        return $query
            ->hasExpiration()
            ->where('expiration_date', '<=', $date);
    }

    public function scopeExpiredForLastDays($query, int $days = 0, bool $addExtra = true)
    {
        $date = Carbon::now()->subDays($days);

        if ($addExtra && ($extraTime = settings('main_settings.extra_expiration_time'))) {
            $date->subSeconds($extraTime);
        }

        return $query->expired()->where('expiration_date', '>=', $date);
    }

    public function scopeUnexpired($query, bool $addExtra = true)
    {
        $date = Carbon::now();

        if ($addExtra && ($extraTime = settings('main_settings.extra_expiration_time'))) {
            $date->subSeconds($extraTime);
        }

        return $query
            ->where(function($q) use ($date) {
                $q->hasntExpiration();
                $q->orWhere(function($q2) use ($date) {
                    $q2->hasExpiration()->where('expiration_date', '>', $date);
                });
            });
    }

    public function scopeHasSimExpiration($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('sim_expiration_date');
            $q->where('sim_expiration_date', '!=', '0000-00-00');
            $q->where('sim_expiration_date', '!=', '0000-00-00 00:00:00');
        });
    }

    public function scopeIsSimExpiringAfter($query, $days)
    {
        return $query
            ->hasSimExpiration()
            ->where('sim_expiration_date', '>=', Carbon::now())
            ->where('sim_expiration_date', '<=', Carbon::now()->addDays($days));
    }

    public function scopeIsSimExpiredBefore($query, $days)
    {
        return $query
            ->hasSimExpiration()
            ->where('sim_expiration_date', '<=', Carbon::now()->subDays($days));
    }

    public function scopeFilterUserAbility($query, User $user, $ability = 'own') {
        return $query->with('users')->get()->filter(function($device) use ($user, $ability) {
            return $user->can($ability, $device);
        });
    }

    public function scopeWhereIdOrImei(Builder $query, $value)
    {
        return $query->where(function (Builder $query) use ($value) {
            $query->where('id', $value);
            $query->orWhere('imei', $value);
        });
    }

    public static function getFields()
    {
        $fields = [
            'name' => trans('validation.attributes.name'),
            'imei' => trans('validation.attributes.imei'),
            'sim_number' => trans('validation.attributes.sim_number'),
            'vin' => trans('validation.attributes.vin'),
            'device_model' => trans('validation.attributes.device_model'),
            'plate_number' => trans('validation.attributes.plate_number'),
            'registration_number' => trans('validation.attributes.registration_number'),
            'object_owner' => trans('validation.attributes.object_owner'),
            'additional_notes' => trans('validation.attributes.additional_notes'),

            //'fuel_quantity' => trans('validation.attributes.fuel_quantity'),
            //'fuel_price' => trans('validation.attributes.fuel_price'),

            'users_emails' => trans('admin.users'),
            'protocol' => trans('front.protocol'),
            'latitude' => trans('front.latitude'),
            'longitude' => trans('front.longitude'),
            'altitude' => trans('front.altitude'),
            'course' => trans('front.course'),
            'speed' => trans('front.speed'),
            'last_connect_time' => trans('admin.last_connection'),
            'stop_duration' => trans('front.stop_duration'),

            'expiration_date' => trans('validation.attributes.expiration_date'),
        ];

        if (settings('plugins.additional_installation_fields.status')) {
            $fields['sim_activation_date'] = trans('validation.attributes.sim_activation_date');
            $fields['sim_expiration_date'] = trans('validation.attributes.sim_expiration_date');
            $fields['installation_date']   = trans('validation.attributes.installation_date');
        }

        return $fields;
    }

    public function getUsersEmailsAttribute()
    {
        return $this
            ->users
            ->filter(function($user){
                return auth()->user()->can('show', $user);
            })
            ->implode('email', ', ');
    }

    public function getImageAttribute()
    {
        $path = Str::finish(self::IMAGE_PATH, '/') . "{$this->id}.*";

        return File::glob($path)[0] ?? null;
    }

    public function getPlanAttribute()
    {
        return $this->plans->first() ?? null;
    }

    public function getPlanIdAttribute()
    {

        return $this->plan->id ?? null;
    }

    public function getNameWithSimNumberAttribute()
    {
        return $this->name." ({$this->sim_number})";
    }

    public function isMove() {
        return $this->getStatus() == self::STATUS_ONLINE;
    }

    public function isIdle() {
        return $this->getStatus() == self::STATUS_ENGINE;
    }

    public function isStop() {
        return $this->getStatus() == self::STATUS_ACK;
    }

    public function isOffline() {
        return $this->getTimeoutStatus() === self::STATUS_OFFLINE;
    }

    public function isPark()
    {
        return $this->isStop() && ! $this->isIdle();
    }

    public function isOfflineFrom($date) {
        $time = strtotime( max($this->getServerTime(), $this->getAckTime()) );

        return Carbon::parse($date)->timestamp > $time;
    }

    public function isInactive()
    {
        $time = strtotime( max($this->getServerTime(), $this->getAckTime()) );

        return Carbon::now()->subMinutes(settings('main_settings.default_object_inactive_timeout'))->timestamp > $time;
    }

    public function isNeverConnected() {
        return is_null($this->getServerTime()) && is_null($this->getAckTime());
    }

    public function wasConnected() {
        return ! $this->isNeverConnected();
    }

    public function scopeTraccarJoin($query) {
        if ($query->isJoined("traccar_devices"))
            return $query;

        //prevent traccar.devices id overwrite
        $selects = $query->getQuery()->columns;
        if (!($selects && in_array('devices.*', $selects))) {
            $query->select('devices.*');
        }

        return $query->leftJoin("traccar_devices", 'devices.traccar_device_id', '=', 'traccar_devices.id');
    }

    public function scopeWasConnected($query) {
        return $query
            ->traccarJoin()
            ->whereNotNull('traccar_devices.server_time');
    }

    public function scopeNeverConnected($query) {
        return $query
            ->traccarJoin()
            ->whereNull('traccar_devices.server_time');
    }

    public function scopeConnectedAfter($query, $time) {
        return $query
            ->traccarJoin()
            ->where('traccar_devices.server_time', '>=', $time);
    }

    public function scopeConnectedBefore($query, $time) {
        return $query
            ->traccarJoin()
            ->where('traccar_devices.server_time', '<', $time);
    }

    public function scopeUpdatedAfter($query, $time)
    {
        return $query
            ->traccarJoin()
            ->where('traccar_devices.updated_at', '>=', $time);
    }

    public function scopeUpdatedBefore($query, $time)
    {
        return $query
            ->traccarJoin()
            ->where('traccar_devices.updated_at', '<', $time);
    }

    public function scopeOnline($query, $minutes = null) {
        if (is_null($minutes))
            $minutes = settings('main_settings.default_object_online_timeout');

        $time = Carbon::now()->subMinutes($minutes);

        return $query->connectedAfter($time);
    }

    public function scopeOffline($query, $minutes = null) {
        if (is_null($minutes))
            $minutes = settings('main_settings.default_object_online_timeout');

        $time = Carbon::now()->subMinutes($minutes);

        return $query
            ->traccarJoin()
            ->where('traccar_devices.server_time', '<', $time);
    }

    public function scopeInactive($query, $minutes = null) {
        if (is_null($minutes))
            $minutes = settings('main_settings.default_object_inactive_timeout');

        return $query->offline($minutes);
    }

    public function scopeMove($query) {

        return $query
            ->traccarJoin()
            ->online()
            ->whereNotNull('traccar_devices.moved_at')
            ->whereRaw('traccar_devices.moved_at > COALESCE(traccar_devices.stoped_at, 0)');
    }

    public function scopeStop($query) {
        return $query
            ->traccarJoin()
            ->wasConnected()
            ->where(function($q) {
                $q->whereNull('traccar_devices.moved_at');
                $q->orWhereRaw('COALESCE(traccar_devices.stoped_at, 0) > traccar_devices.moved_at');
            });
    }

    public function scopeStopDuration($query, $minutes)
    {
        $time = Carbon::now()->subMinutes($minutes);

        return $query
            ->stop()
            ->where('traccar_devices.stop_begin_at', '<', $time);
    }

    public function scopePark($query) {
        return $query
            ->engineOff()
            ->stop()
            ->online();
    }

    public function scopeIdle($query) {
        return $query
            ->engineOn()
            ->stop()
            ->online();
    }

    public function scopeEngineOn($query) {
        return $query
            ->traccarJoin()
            ->online()
            ->whereNotNull('traccar_devices.engine_on_at')
            ->whereRaw('traccar_devices.engine_on_at > COALESCE(traccar_devices.engine_off_at, 0)');
    }

    public function scopeEngineOff($query) {
        return $query
            ->traccarJoin()
            ->online()
            ->whereNotNull('traccar_devices.engine_off_at')
            ->whereRaw('traccar_devices.engine_off_at > COALESCE(traccar_devices.engine_on_at, 0)');
    }

    public function scopeProtocol($query, $protocol)
    {
        $query->traccarJoin();

        if (is_null($protocol))
            $query->whereNull('traccar_devices.protocol');

        if (is_array($protocol))
            $query->whereIn('traccar_devices.protocol', $protocol);

        if (is_string($protocol))
            $query->where('traccar_devices.protocol', $protocol);

        return $query;
    }

    public function scopeGroupProtocols($query)
    {
        $query
            ->traccarJoin()
            ->with('traccar')
            ->groupBy('traccar_devices.protocol')
            ->whereNotNull('traccar_devices.protocol');

        return $query;
    }

    /**
     * @param  Builder  $query
     * @param int|int[] $ids
     * @return Builder
     */
    public function scopeInGeofences(Builder $query, $ids): Builder
    {
        if (!is_array($ids)) {
            $ids = (array)$ids;
        }

        return $query
            ->traccarJoin()
            ->whereNotNull(['traccar_devices.lastValidLatitude', 'traccar_devices.lastValidLongitude'])
            ->whereExists(function ($query) use ($ids) {
                $query = (new Builder($query))
                    ->setModel(new Geofence())
                    ->whereIn('id', $ids)
                    ->containPoint('traccar_devices.lastValidLatitude', 'traccar_devices.lastValidLongitude');
            });
    }

    public function scopeKindGeneral(Builder $query): Builder
    {
        return $query->where('kind', self::KIND_GENERAL);
    }

    public function scopeKindBeacon(Builder $query): Builder
    {
        return $query->where('kind', self::KIND_BEACON);
    }

    public function isGeneral(): bool
    {
        return $this->kind === self::KIND_GENERAL;
    }

    public function isBeacon(): bool
    {
        return $this->kind === self::KIND_BEACON;
    }

    public function activate(DevicePlan $plan, $expirationDate)
    {
        $this->plans()->sync([$plan->id]);

        $this->update([
            'expiration_date' => $expirationDate,
        ]);

        event(new DeviceSubscriptionActivate($this));
    }

    public function renew($expirationDate)
    {
        $this->setExpirationDate($expirationDate);

        event(new DeviceSubscriptionRenew($this));
    }

    public function setExpirationDate($expirationDate)
    {
        $this->update([
            'expiration_date' => $expirationDate,
        ]);
    }
}
