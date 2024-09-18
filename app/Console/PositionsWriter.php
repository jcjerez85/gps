<?php

namespace App\Console;

use App\Events\DeviceEngineChanged;
use App\Events\DevicePositionChanged;
use App\Events\PositionResultRetrieved;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Tobuli\Entities\Device;
use Tobuli\Entities\Event;
use Tobuli\Entities\SendQueue;
use Tobuli\Entities\TraccarPosition as Position;
use CustomFacades\Repositories\UserDriverRepo;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag as Bugsnag;
use Tobuli\Helpers\Alerts\Checker;
use Tobuli\Services\EventWriteService;

class PositionsWriter
{
    const MIN_DISTANCE = 0.02;
    const PARKED_MIN_DURATION = 120;

    /**
     * @var Device
     */
    protected $device;

    protected $events = [];

    /**
     * @var Position[]
     */
    protected $positions = [];

    protected $position = null;

    protected $prevPosition = null;

    protected $drivers = [];

    protected $alertChecker = null;

    protected $debug;

    protected $beaconsPositionWriter;

    protected $max_speed = null;
    protected $min_time_gap = null;
    protected $prev_position_device_object = null;

    protected $eventWriteService = null;

    public function __construct($device, $debug = false)
    {
        $this->device = $device;

        $this->debug = $debug;

        $this->beaconsPositionWriter = new PositionsBeaconsWriter($device, $debug);
        $this->stack = new PositionsStack();

        $this->device->load(['timezone']);

        $this->max_speed = config('tobuli.max_speed');
        $this->min_time_gap = config('tobuli.min_time_gap');

        $this->prev_position_device_object = config('tobuli.prev_position_device_object');

        $this->apply_network_data = config('tobuli.apply_network_data');

        $this->eventWriteService = new EventWriteService();

        $this->forwards = new PositionsForward($this->device, $this->debug);
    }

    protected function line($text = '')
    {
        if ( ! $this->debug)
            return;

        echo $text . PHP_EOL;
    }

    public function runList($imei)
    {
        $key = 'positions.' . $imei;

        if ($this->debug) {
            $this->line('IMEI: ' . $imei);
            $this->line('Keys: ' . $this->stack->oneCount($key));
            $this->line('Database ID: ' . $this->device->traccar->database_id);
        }

        $p = 0;
        $n = 0;
        $start = microtime(true);

        foreach($this->stack->getKeyDataList($key) as $data) {
            $s = microtime(true);

            $data = $this->normalizeData($data);

            $n += microtime(true) - $s;

            if ( ! $data )
                continue;

            $this->proccess($data);
            $this->resetPrevPosition();

            $p += microtime(true) - $s;
        }

        if ($this->debug) {
            $this->line('Keys Process only ' . ($p));
            $this->line('Keys Normalize Time ' . ($n));
            $this->line('Keys Getting Time ' . (microtime(true) - $start - $p));
            $this->line('Keys Process Time ' . (microtime(true) - $start));
        }

        $this->write();
    }

    protected function normalizeData($data)
    {
        if ( ! empty($data['deviceId']))
            $data['imei'] = $data['deviceId'];

        if ( ! empty($data['uniqueId']))
            $data['imei'] = $data['uniqueId'];

        if (empty($data['imei']))
            return false;

        $data = array_merge([
            'altitude'  => 0,
            'course'    => null,
            'latitude'  => null,
            'longitude' => null,
            'speed'     => 0,
            'distance'  => 0,
            'valid'     => 1,
            'protocol'  => null,

            'ack'         => empty($data['fixTime']),
            'attributes'  => [],
            'server_time' => date('Y-m-d H:i:s'),
        ], $data);

        $data['speed'] = $data['speed'] * 1.852;

        if ($data['ack']) {
            if ( ! empty($data['deviceTime'])) {
                $data['device_time'] = date('Y-m-d H:i:s', $data['deviceTime'] / 1000);
            }
            else {
                $data['device_time'] = null;
            }
        } else {
            $data['device_time'] = date('Y-m-d H:i:s', $data['fixTime'] / 1000);
        }

        if (is_null($data['device_time']))
        {
            $data['device_time'] = $this->device->getDeviceTime();
        }

        $data['time'] = $data['device_time'];

        if ($this->device->timezone)
        {
            $data['time'] = date('Y-m-d H:i:s', strtotime($this->device->timezone->zone, strtotime($data['time'])));
        }

        if ($data['time'] == $this->device->getTime() && time() - strtotime($this->device->getServerTime()) > 60)
            $data['ack'] = true;

        if ($this->isSkipableOsmand($data)) {
            $this->line('Osmand skipable');
            return false;
        }


        if ( ! $data['ack']) {
            //Outdated check for 90 days
            if (time() - strtotime($data['time']) > 7776000) {
                $this->line('Bad date - outdated: ' . $data['time']);
                return false;
            }

            //Future check for 1 day
            if (strtotime($data['time']) - time() > 86400) {
                $this->line('Bad date - future: ' . $data['time']);
                return false;
            }
        }

        if ($overwrite = $this->getProtocolConfig($data['protocol'], 'overwrite'))
            $data['protocol'] = $overwrite;

        if ($this->getProtocolConfig($data['protocol'], 'bypass_invalid'))
            $data['valid'] = 1;

        $parameters = [];
        foreach ((is_array($data['attributes']) ? $data['attributes'] : []) as $key => $value) {
            $key = preg_replace('/[^a-zA-Z0-9_-]/s','', $key);
            $key = strtolower($key);
            $parameters[$key] = $value;
        }
        $parameters['valid'] = $data['valid'];
        $parameters[Position::VIRTUAL_ENGINE_HOURS_KEY] = 0;

        if ($this->apply_network_data && $networkData = Arr::get($data, 'network.cellTowers.0')) {
            $parameters = array_merge($parameters, $networkData);
        }
        $gsmSignal = Arr::get($data, 'network.cellTowers.0.signalStrength');
        if (!is_null($gsmSignal))
            $parameters['gsmsignal'] = $gsmSignal;

        if ( $this->getProtocolConfig($data['protocol'], 'mergeable') && $prevPosition = $this->getPrevPosition($data['time']) ) {
            $excepts = $this->getProtocolConfig($data['protocol'], 'expects') ?? [];
            $excepts = array_merge(['alarm', 'result', 'sat'], $excepts);

            $prevParameters = Arr::except($prevPosition->parameters, $excepts);
            $parameters = array_merge($prevParameters, $parameters);
        }

        if ( ! empty($parameters['ip']))
            unset($parameters['ip']);

        $data['parameters'] = $parameters;

        $params = empty($this->device->parameters) ? [] : json_decode($this->device->parameters, true);
        $params = empty($params) ? [] : array_flip($params);
        $params = array_map(function($val) { return strtolower($val); }, $params);

        $merge = array_keys(array_merge($parameters, $params));
        if (count($params) != count($merge)) {
            $this->device->parameters = json_encode($merge);
        }

        return $data;
    }

    protected function isHistory($time = null)
    {
        if (is_null($time) && $this->position)
            $time = $this->position->time;

        return strtotime($time) < strtotime($this->device->getTime());
    }

    protected function isChanged($current, $previous)
    {
        if (empty($previous))
            return true;

        if (round($current->speed, 1) != round($previous->speed, 1))
            return true;

        if ($current->distance > self::MIN_DISTANCE)
            return true;

        if ((strtotime($current->time) - strtotime($previous->time)) >= $this->min_time_gap)
            return true;

        $escape = [
            'distance',
            'totaldistance',
            'sequence',
            'power',
            'index',
            'axisx',
            'axisy',
            'axisz',
            Position::VIRTUAL_ENGINE_HOURS_KEY
        ];

        $currentParameters  = Arr::except($current->parameters, $escape);
        $previousParameters = Arr::except($previous->parameters, $escape);

        if ($currentParameters != $previousParameters)
            return true;

        return false;
    }

    protected function getPrevPosition($time = null)
    {
        if ( ! is_null($this->prevPosition))
            return $this->prevPosition;

        if (is_null($time) && $this->position)
            $time = $this->position->time;

        if (empty($time))
            return $this->getLastPosition();

        $_time = strtotime($time);

        if ($this->positions) {
            foreach ($this->positions as $position) {
                $positionTime = strtotime($position->time);

                if ($positionTime > $_time)
                    break;

                $this->prevPosition = $position;

                if ($positionTime < $_time)
                    continue;

                break;
            }
        }

        if ($this->prevPosition && $this->isHistory($time)) {
            $prevTime = strtotime($this->prevPosition->time);
            $timeDiff = $_time - $prevTime;

            if ($timeDiff > 300 || $timeDiff < 0) {
                $this->line('Getting history prev with time ' . $time);

                $storedPosition = $this->getPrevHistoryPosition($time);

                if ($storedPosition && strtotime($storedPosition->time) > $prevTime)
                    $this->prevPosition = $storedPosition;
            }
        }

        if ($this->prev_position_device_object && is_null($this->prevPosition) && ! $this->isHistory($time))
            $this->prevPosition = $this->getLastPosition();

        if (is_null($this->prevPosition))
        {
            $this->line('Getting history prev with null');

            $this->prevPosition = $this->getPrevHistoryPosition($time);
        }

        return $this->prevPosition;
    }

    protected function getPrevValidPosition($time = null)
    {
        if (is_null($time) && $this->position)
            $time = $this->position->time;

        $prevPosition = $this->getPrevPosition($time);

        if ($prevPosition && $prevPosition->isValid())
            return $prevPosition;

        if (!$this->isHistory() && $current = $this->getLastPosition()) {

            if (filter_var($current->getParameter('valid'), FILTER_VALIDATE_BOOLEAN))
                return $current;

            $distance = getDistance(
                $this->position->latitude,
                $this->position->longitude,
                $current->latitude,
                $current->longitude
            );

            if ($distance < 0.1)
                return $current;
        }

        $this->line('Getting previous valid');

        return $this->getPrevHistoryPosition($time, true);
    }

    protected function getPrevHistoryPosition($time, $onlyValid = false)
    {
        try {
            return $this->device->positions()
                ->orderliness()
                ->where('time', '<=', $time)
                ->when($onlyValid, function($query) {
                    $query->where('valid', '>', 0);
                })
                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getLastPosition()
    {
        if ( ! $this->device->traccar)
            return null;

        if (empty($this->device->traccar->lastValidLatitude) && empty($this->device->traccar->lastValidLongitude))
            return null;

        $position = new Position([
            'server_time' => $this->device->traccar->server_time,
            'device_time' => $this->device->traccar->device_time,
            'time'        => $this->device->traccar->time,
            'latitude'    => $this->device->traccar->lastValidLatitude,
            'longitude'   => $this->device->traccar->lastValidLongitude,
            'speed'       => $this->device->traccar->speed,
            'course'      => $this->device->traccar->course,
            'altitude'    => $this->device->traccar->altitude,
            'protocol'    => $this->device->traccar->protocol,
            'other'       => $this->device->traccar->other,
        ]);

        $position->id    = $this->device->traccar->latestPosition_id;
        $position->valid = 1;
        //$position->valid = filter_var($position->getParameter('valid'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        return $position;
    }

    protected function proccess($data)
    {
        if ($result = $data['parameters']['result'] ?? null) {
            event(new PositionResultRetrieved($this->device, is_scalar($result) ? $result : json_encode($result)));
        }

        if ( ! $this->device->traccar)
            return;

        $this->position = new Position($data);
        $this->position->ack = $data['ack'];

        $prevPosition = $this->getPrevPosition();
        $this->position->setParameter('totaldistance', $prevPosition ? $prevPosition->getParameter('totaldistance', 0) : 0);
        $this->position->setParameter('valid', $this->position->isValid() ? 'true' : 'false');
        $this->position->setParameter(Position::VIRTUAL_ENGINE_HOURS_KEY, $this->getVirtualEngineHours());

        if ($this->position->ack && $this->isHistory($this->position->time))
            return;

        $lastValidPosition = $this->getPrevValidPosition();

        if (!$this->isValidPositionLatLng($this->position))
        {
            if ($lastValidPosition)
            {
                $this->position->latitude = $lastValidPosition->latitude;
                $this->position->longitude = $lastValidPosition->longitude;
            }
            else
            {
                $this->position->valid = 0;
            }
        }

        if ($this->position->speed > $this->max_speed)
            $this->position->speed = $lastValidPosition ? $lastValidPosition->speed : $this->max_speed;

        //if (is_null($this->position->course) && $lastValidPosition)
        //    $this->position->course = $lastValidPosition->course;

        if (empty($this->position->course) && $lastValidPosition)
            $this->position->course = getCourse(
                $this->position->latitude,
                $this->position->longitude,
                $lastValidPosition->latitude,
                $lastValidPosition->longitude
            );


        if ($this->position->valid && $lastValidPosition)
        {
            $this->position->distance = getDistance(
                $this->position->latitude,
                $this->position->longitude,
                $lastValidPosition->latitude,
                $lastValidPosition->longitude
            );

            $skipProtocols = ['upro'];

            if (
                $this->device->valid_by_avg_speed &&
                ! in_array($this->position->protocol, $skipProtocols) &&
                $this->position->distance > 10 &&
                $this->getLastPosition() && $this->getLastPosition()->id > 50
            )
            {
                $time = strtotime($this->position->time) - strtotime($lastValidPosition->time);

                if ($time > 0) {
                    $avg_speed = $this->position->distance / ($time / 3600);

                    if ($avg_speed > $this->max_speed) {
                        $this->position->valid = 0;
                    }
                } else {
                    $this->position->valid = 0;
                }
            }
        }

        //tmp
        if ( ! $this->position->isValid())
        {
            $this->position->distance = 0;

            if ($lastValidPosition)
            {
                $this->position->latitude = $lastValidPosition->latitude;
                $this->position->longitude = $lastValidPosition->longitude;
            }
        }

        $distance = round($this->position->distance * 1000, 2);

        $this->position->setParameter('distance', $distance);


        $totalDistance = $lastValidPosition ? $lastValidPosition->getParameter('totaldistance', 0) : 0;

        if ($this->position->isValid())
        {
            $totalDistance += $distance;
        }

        $this->position->setParameter('totaldistance', $totalDistance);
        $this->position->setParameter('valid', $this->position->isValid() ? 'true' : 'false');
        $this->position->setParameter(Position::VIRTUAL_ENGINE_HOURS_KEY, $this->getVirtualEngineHours());

        $this->setSensors();

        if ($this->checkableAlerts()) {
            $this->alerts();
        }

        if ($this->events || $this->isChanged($this->position, $this->getPrevPosition())) {
            $this->addPosition($this->position);
        }


        $this->setTraccarDeviceMovedAt($this->position);
        $this->setTraccarDeviceStopedAt($this->position);
        $this->setTraccarDeviceParkEndAt($this->position);
        $this->setTraccarDeviceMoveBeginAt($this->position);
        $this->setTraccarDeviceStopBeginAt($this->position);
        $this->setTraccarDeviceEngineAt($this->position);

        if ( ! $this->isHistory())
        {
            if ($this->position->isValid()) {
                $this->setTraccarDevicePosition($this->position);
            }

            $this->setTraccarDeviceData($this->position);
        }

        $this->setCurrentDriver($this->position);

        $this->forwards->process($this->position);

        if ($this->events || count($this->positions) > 100) {
            $this->write();
        }
    }

    protected function getEngineStatus($position)
    {
        if ( ! isset($this->engine_sensor))
            $this->engine_sensor = $this->device->getEngineSensor();

        if ($this->engine_sensor)
            return $this->engine_sensor->getValue($position->other, false, null);

        return $position->speed > 0;
    }

    protected function getVirtualEngineHours()
    {
        $prevPosition = $this->getPrevPosition();

        if (!$prevPosition)
            return 0;

        $engineHours = $prevPosition->getVirtualEngineHours();

        $duration = strtotime($this->position->time) - strtotime($prevPosition->time);

        if ($duration < 1)
            return $engineHours;

        //skip if duration between positions is more then 5 mins
        $timeout = max(5, settings('main_settings.default_object_online_timeout')) * 60;
        if ($duration > $timeout)
            return $engineHours;

        if ( ! isset($this->engine_hours_sensor))
            $this->engine_hours_sensor = $this->device->getEngineHoursSensor();

        if ($this->engine_hours_sensor && $this->engine_hours_sensor->shown_value_by == 'logical') {
            $prevEngineStatus = $this->engine_hours_sensor->getValueParameters($prevPosition->other);
        } else {
            $prevEngineStatus = $this->getEngineStatus($prevPosition);
        }

        if ( ! $prevEngineStatus)
            return $engineHours;

        return $engineHours + $duration;
    }

    protected function alerts()
    {
        if (is_null($this->alertChecker))
        {
            $start = microtime(true);

            $alerts = $this->device
                ->alerts()
                ->with('user', 'geofences', 'drivers', 'events_custom', 'zones')
                ->withPivot('started_at', 'fired_at', 'active_from', 'active_to')
                ->checkByPosition()
                ->active()
                ->get();

            if ($count = count($alerts)) {
                $this->alertChecker = new Checker($this->device, $alerts);

                $this->line('Alerts: '.count($alerts).' '.$alerts->implode('type', ','));
            } else {
                $this->alertChecker = false;
            }

            $end = microtime(true);
            $this->line('Alerts getting time '.round($end - $start, 5));
        }

        if ($this->alertChecker === false)
            return;

        $start = microtime(true);

        // reset device with new proterties as lat, lng and etc.
        $this->alertChecker->setDevice($this->device);

        $this->events = $this->alertChecker->check($this->position, $this->getPrevPosition());

        $end = microtime(true);
        $this->line('Alerts check time '.round($end - $start, 5));
    }

    protected function checkableAlerts()
    {
        if (!$this->device->traccar)
            return false;

        if ($this->device->isExpired())
            return false;

        $timePosition = strtotime($this->position->time);
        $timeDevice = strtotime($this->device->traccar->getOriginal('time'));

        return $timePosition >= $timeDevice;
    }

    protected function setSensors()
    {
        $sensorsValues = [];

        if ($this->device->sensors) {
            foreach ($this->device->sensors as &$sensor) {
                $sensorValue = null;

                if ( $sensor->isCounter()) {
                    $sensorValue = intval($sensor->getValueParameters($this->position->other));

                    if ($sensorValue)
                        $sensor->setCounter($sensorValue);

                    $sensorsValues[] = [
                        'id'  => $sensor->id,
                        'val' => $sensor->getCounter()
                    ];

                    continue;
                }

                if ( $sensor->isUpdatable() && ! $this->isHistory()) {
                    $sensorValue = $sensor->getValue($this->position->other);

                    if ( $sensor->isValid($sensorValue))
                        $sensor->setValue($sensorValue);
                }

                if ( ! $sensor->isPositionValue())
                    continue;

                if ($this->isHistory()) {
                    $prevSensorValue = null;

                    if ($prevPosition = $this->getPrevPosition())
                    {
                        $prevSensorValue = $sensor->getValue($prevPosition->other, false);
                    }

                    $sensorValue = $sensor->getValue($this->position->other, false, $prevSensorValue);
                } elseif(is_null($sensorValue)) {
                    $sensorValue = $sensor->getValue($this->position->other);
                }

                if ( ! is_null($sensorValue)) {
                    $sensorsValues[] = [
                        'id'  => $sensor->id,
                        'val' => $sensorValue
                    ];
                }
            }
        }

        if ($sensorsValues)
            $this->position->sensors_values = json_encode($sensorsValues);
    }

    protected function getRFIDs($position)
    {
        if ( ! isset($this->rfid_sensor))
            $this->rfid_sensor = $this->device->getRfidSensor();

        if ($this->rfid_sensor) {
            $rfid = $this->rfid_sensor->getValue($position->other, false, null);
            return $rfid ? [$rfid] : null;
        }

        return $position->getRfids();
    }

    protected function setCurrentDriver($position)
    {
        $rfids = $this->getRfids($position);

        if ( ! $rfids)
            return;

        $hash = md5(json_encode($rfids));

        if ( ! array_key_exists($hash, $this->drivers))
        {
            $this->drivers[$hash] = UserDriverRepo::findWhere(function($query) use ($rfids){
                $query->whereIn('rfid', $rfids);
            });
        }

        $driver = $this->drivers[$hash];

        if ( ! $driver)
            return;

        if ($this->device->current_driver_id == $driver->id)
            return;

        $this->device->current_driver_id = $driver->id;

        $driver->changeDevice($this->device, $position->time, true);
    }

    protected function setTraccarDevicePosition($position)
    {
        $this->device->traccar->lastValidLatitude = $position->latitude;
        $this->device->traccar->lastValidLongitude = $position->longitude;
        $this->device->traccar->altitude = $position->altitude;
        //$this->device->traccar->speed = $position->speed;
        $this->device->traccar->course = $position->course;


        $latest_positions = $this->device->traccar->latest_positions ? explode(';', $this->device->traccar->latest_positions) : [];

        if ( ! $latest_positions) {
            array_unshift($latest_positions, $position->latitude . '/' . $position->longitude);
        } else {
            list($lat, $lng) = explode('/', reset($latest_positions));

            $distance = getDistance($position->latitude, $position->longitude, $lat, $lng);

            if ($distance > self::MIN_DISTANCE)
                array_unshift($latest_positions, $position->latitude . '/' . $position->longitude);
        }

        $this->device->traccar->latest_positions = implode(';', array_slice($latest_positions, 0, 15));
    }

    protected function setTraccarDeviceData($position)
    {
        $this->device->traccar->time = $position->time;
        $this->device->traccar->server_time = $position->server_time;
        $this->device->traccar->updated_at = $position->server_time;
        $this->device->traccar->device_time = $position->device_time;
        $this->device->traccar->other = $position->other;
        $this->device->traccar->protocol = $position->protocol;
        $this->device->traccar->speed = $position->speed;

        if ($position->ack) {
            $this->device->traccar->speed = 0;
            $this->device->traccar->ack_time = $position->server_time;
        }
    }

    protected function setTraccarDeviceMovedAt($position)
    {
        if (!$this->isDeviceMovingAtPosition($position))
            return;

        if ($this->device->traccar->moved_at > $position->time)
            return;

        $this->device->traccar->moved_at = $position->time;
    }

    protected function setTraccarDeviceStopedAt($position)
    {
        if ($this->isDeviceMovingAtPosition($position))
            return;

        if ($this->device->traccar->stoped_at > $position->time)
            return;

        $this->device->traccar->stoped_at = $position->time;
    }

    protected function setTraccarDeviceParkEndAt($position)
    {
        if ($position->time < $this->device->traccar->stop_begin_at) {
            return;
        }

        if ($this->device->traccar->stoped_at > $this->device->traccar->moved_at) {
            return;
        }

        if ($this->device->traccar->parked_end_at > $this->device->traccar->stoped_at) {
            return;
        }

        $stopDuration = strtotime($position->time) - strtotime($this->device->traccar->stop_begin_at);

        if ($stopDuration >= self::PARKED_MIN_DURATION) {
            $this->device->traccar->parked_end_at = $position->time;
        }
    }

    protected function setTraccarDeviceMoveBeginAt($position)
    {
        //is stoped
        if ($this->device->traccar->stoped_at > $this->device->traccar->moved_at) {
            return;
        }

        if ($this->device->traccar->move_begin_at > $this->device->traccar->stoped_at) {
            return;
        }

        if ($this->device->traccar->move_begin_at > $position->time) {
            return;
        }

        $this->device->traccar->move_begin_at = $position->time;
    }

    protected function setTraccarDeviceStopBeginAt($position)
    {
        //is moving
        if ($this->device->traccar->moved_at > $this->device->traccar->stoped_at) {
            return;
        }

        if ($this->device->traccar->stop_begin_at > $this->device->traccar->moved_at) {
            return;
        }

        if ($this->device->traccar->stop_begin_at > $position->time) {
            return;
        }

        $this->device->traccar->stop_begin_at = $position->time;
    }

    protected function setTraccarDeviceEngineAt($position)
    {
        $status = $this->getEngineStatus($position);
        $prevStatus = $this->device->traccar->engine_on_at > $this->device->traccar->engine_off_at;

        if ($status != $prevStatus) {
            $this->device->traccar->engine_changed_at = $position->time;
        }

        if ($status)
            $this->device->traccar->engine_on_at = $position->time;
        else
            $this->device->traccar->engine_off_at = $position->time;
    }

    protected function wasDeviceMoving(): bool
    {
        return $this->device->traccar->moved_at > $this->device->traccar->stoped_at;
    }

    protected function isDeviceMovingAtPosition($position): bool
    {
        if (empty($position))
            return false;

        return $position->speed > $this->device->min_moving_speed;
    }

    protected function addPosition($position)
    {
        $this->positions[] = $position;

        $this->positions = Arr::sort($this->positions, function($value){
            return $value->time;
        });

        $this->resetPrevPosition();
    }

    protected function updatePosition($position)
    {
        $this->line('Updating last position...');

        // skip if new position
        if ( ! $position->id)
            return;

        // skip if position already in list
        if(array_filter($this->positions, function($value) use ($position) { return $position->id == $value->id; }))
            return;

        $this->addPosition($position);
    }

    protected function resetPrevPosition()
    {
        $this->prevPosition = null;
    }

    protected function write()
    {
        $this->line('Writing:');
        $this->line('Positions '.count($this->positions));
        $this->line('Events '.count($this->events));

        $start = microtime(true);

        $this->beaconsPositionWriter->write($this->positions);
        $this->writePositions();
        $this->writeEvents();

        foreach ($this->device->sensors as $sensor) {
            $sensor->save();
        }

        if ($this->device->traccar) {
            $positionChanged = $this->device->traccar->isDirty(['lastValidLatitude', 'lastValidLongitude']);
            $engineChanged = $this->device->traccar->isDirty(['engine_changed_at']);

            $this->device->traccar->save();

            if ($positionChanged)
                event(new DevicePositionChanged($this->device));

            if ($engineChanged)
                event(new DeviceEngineChanged($this->device));
        }

        $this->device->save();

        $end = microtime(true);

        $this->line('Write time '.($end - $start));

        $this->forwards->send();
    }

    protected function writePositions()
    {
        if ( ! $this->positions)
            return;

        $data = [];

        foreach ($this->positions as $position)
        {
            if ($position->id)
            {
                $this->line('Saving updated position...');
                $position->save();
                continue;
            }

            $attributes = $position->attributesToArray();

            if ($position->getFillable()) {
                $attributes = array_intersect_key($attributes, array_flip($position->getFillable()));
            }

            if (empty($attributes['sensors_values'])) {
                $attributes['sensors_values'] = null;
            } elseif (is_array($attributes['sensors_values'])) {
                $attributes['sensors_values'] = json_encode($attributes['sensors_values']);
            }

            $data[] = $attributes;
        }

        $this->positions = [];

        $count = count($data);

        if ( ! $count)
            return;

        try {
            $this->writePositionData($data, $count > 1);
        } catch (\Exception $e) {
            if ($e->getCode() == '42S02') {
                $this->device->createPositionsTable();
                $this->writePositionData($data, $count > 1);
            } else {
                Bugsnag::notifyException($e);
            }
        }
    }

    protected function writePositionData($data, $multi) {
        if ($multi)
        {
            $this->device->positions()->insert($data);
            $lastPosition = $this->device->positions()->orderliness()->first();
            $this->device->traccar->latestPosition_id = $lastPosition->id;

        } else
        {
            $position = $this->device->positions()->create($data[0]);

            if ( ! $this->isHistory())
                $this->device->traccar->latestPosition_id = $position->id;
        }
    }

    protected function writeEvents()
    {
        if ( ! $this->events)
            return;

        $insertedPosition = $this->device->positions()->orderBy('time', 'desc')->first();

        if ( ! $insertedPosition) {
            $this->events = [];
            return;
        }

        $this->events = array_map(function($event) use ($insertedPosition) {
            $event->position_id = $insertedPosition->id;
            return $event;
        }, $this->events);

        $this->eventWriteService->write($this->events);

        $this->events = [];
    }

    protected function getProtocolConfig($protocol, $key)
    {
        $config = \Illuminate\Support\Facades\Cache::store('array')->rememberForever("protocol.$protocol", function() use ($protocol) {
            return settings("protocols.$protocol");
        });

        if (empty($config))
            return null;

        return Arr::get($config, $key);
    }

    protected function isSkipableOsmand($data)
    {
        if (!config('addon.device_tracker_app_login'))
            return false;

        if ($data['protocol'] != 'osmand')
            return false;

        $protocol = $this->device->traccar->protocol ?? null;

        if (is_null($protocol))
            return false;

        if ($protocol == 'osmand')
            return false;

        return true;
    }

    protected function isValidPositionLatLng($position)
    {
        if (empty($position->latitude) && empty($position->longitude))
            return false;

        if ($position->latitude < -90)
            return false;

        if ($position->latitude > 90)
            return false;

        if ($position->longitude < -180)
            return false;

        if ($position->longitude > 180)
            return false;

        return true;
    }
}