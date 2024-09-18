<?php namespace ModalHelpers;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Tobuli\Entities\Device;
use Tobuli\Entities\UserDriver;
use Tobuli\History\Actions\AppendRouteColor;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GroupDriveStop;
use Tobuli\History\Actions\Positions;
use Tobuli\History\Actions\Speed;
use Tobuli\History\DeviceHistory;
use Validator;
use App\Exceptions\ResourseNotFoundException;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Repositories\EventRepo;
use CustomFacades\Validators\HistoryFormValidator;
use Illuminate\Support\Facades\Config;
use Tobuli\Exceptions\ValidationException;
use Formatter;

ini_set('memory_limit', '-1');
set_time_limit(600);

class HistoryModalHelper extends ModalHelper {

    const STATUS_DRIVE = 1;
    const STATUS_STOP  = 2;
    const STATUS_START = 3;
    const STATUS_END   = 4;
    const STATUS_EVENT = 5;

    public function __construct()
    {
        parent::__construct();

        $this->icons = [
            self::STATUS_STOP => [
                'iconUrl' => asset('assets/images/route_stop.png'),
                'iconSize' => [32, 32],
                'iconAnchor' => [16, 32]
            ],
            self::STATUS_START => [
                'iconUrl' => asset('assets/images/route_start.png'),
                'iconSize' => [32, 32],
                'iconAnchor' => [16, 32]
            ],
            self::STATUS_END => [
                'iconUrl' => asset('assets/images/route_end.png'),
                'iconSize' => [32, 32],
                'iconAnchor' => [16, 32]
            ],
            self::STATUS_EVENT => [
                'iconUrl' => asset('assets/images/route_event.png'),
                'iconSize' => [32, 32],
                'iconAnchor' => [16, 32]
            ],
        ];
    }

    public function getDevice()
    {
        /** @var Device $device */

        if (!empty($this->data['imei'])) {
            $device = DeviceRepo::whereImei($this->data['imei']);
        } else {
            $device = DeviceRepo::find($this->data['device_id']);
        }

        $this->checkException('devices', 'own', $device);

        if ($device->isExpired())
            throw new ValidationException(['id' =>  trans('front.expired')]);

        return $device;
    }

    public function getHistoryData($device)
    {
        $this->checkException('history', 'view');

        $date_from = Formatter::time()->reverse($this->data['from_date'].' '.$this->data['from_time']);
        $date_to   = Formatter::time()->reverse($this->data['to_date'].' '.$this->data['to_time']);

        if (Carbon::parse($date_from)->diffInDays($date_to) > Config::get('tobuli.max_history_period_days'))
            throw new ValidationException([
                'id' => strtr(trans('front.to_large_date_diff'), [':days' => Config::get('tobuli.max_history_period_days')])
            ]);


        $history = new DeviceHistory($device);
        $history->setConfig([
            'stop_seconds'      => Arr::get($this->data, 'stops', 180),
            'stop_speed'        => $device->min_moving_speed,
            'min_fuel_fillings' => $device->min_fuel_fillings,
            'min_fuel_thefts'   => $device->min_fuel_thefts,
        ]);
        $history->setRange($date_from, $date_to);

        $history->registerActions([
            AppendRouteColor::class,
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            EngineHours::class,
            Drivers::class,
            Positions::class,
            GroupDriveStop::class
        ]);

        $history->setSensors($this->getDeviceSensors($device));

        return $history->get();
    }

    private function getDeviceSensors($device)
    {
        return $device->sensors->filter(function($sensor) {
            return in_array($sensor->type, [
                'fuel_tank',
                'fuel_tank_calibration',
                'fuel_consumption',
                'temperature',
                'temperature_calibration',
                'odometer',
                'tachometer',
                'gsm',
                'battery',
                'load',
                'load_calibration',
                'ignition',
                'engine',
                'speed_ecm',
                'engine_hours'
            ]);
        });
    }

    public function get() {

        HistoryFormValidator::validate('create', $this->data);

        $device = $this->getDevice();

        $snapToRoad = (isset($this->data['snap_to_road']) && ($this->data['snap_to_road'] == 'true' || $this->data['snap_to_road'] == 1)) ? true : false;

        $data = $this->getHistoryData($device);

        $items = [];

        if ($data['root']->getStartPosition()) {

            $events = EventRepo::search(['device_id' => $device->id, 'user_id' => $this->user->id] + $this->data);

            $items[] = [
                'status'   => self::STATUS_START,
                'icon'      => $this->icons[self::STATUS_START] ?? null,
                'lat'       => $data['root']->getStartPosition()->latitude,
                'lng'       => $data['root']->getStartPosition()->longitude,
                'start'     => $this->getPositionLocation($data['root']->getStartPosition()),
                'metas'     => $this->getGroupMetas($data['root'], [
                    'driver',
                    'distance',
                    'drive_duration',
                    'stop_duration',
                    'speed_max',
                    'fuel_consumption',
                    'engine_hours'
                ]),
            ];

            foreach ($data['groups']->all() as $group) {

                while ($event = Arr::first($events)) {
                    if (strtotime($group->getStartPosition()->time) < strtotime($event['time'])) {
                        break;
                    }

                    array_shift($events);

                    $items[] = $this->getEventPosition($event);
                }

                $status = $group->getKey() == 'stop' ? self::STATUS_STOP : self::STATUS_DRIVE;

                if ($status == self::STATUS_STOP) {
                    $metas = [
                        'driver',
                        'speed',
                        'altitude',
                        'duration',
                        'fuel_consumption',
                        'engine_hours'
                    ];
                } else {
                    $metas = [
                        'driver',
                        'speed_max',
                        'distance',
                        'duration',
                        'fuel_consumption',
                        'engine_hours'
                    ];
                }

                $positions = $group->getStat('positions')->value();

                $item = [
                    'status'    => $status,
                    'icon'      => $this->icons[$status] ?? null,
                    'start'     => $this->getPositionLocation($group->getStartPosition()),
                    'end'       => $this->getPositionLocation($group->getEndPosition()),
                    'metas'     => $this->getGroupMetas($group, $metas),
                    'positions' => array_map(
                        function ($position) {
                            $data = [
                                'id' => $position->id,
                                't' => Formatter::time()->convert($position->time),
                                'a' => Formatter::altitude()->format($position->altitude),
                                's' => Formatter::speed()->format($position->speed),
                                'c' => $position->color,
                                'v' => $position->valid,
                                'lat' => $position->latitude,
                                'lng' => $position->longitude,
                            ];

                            if (!empty($position->sensors)) {
                                foreach ($position->sensors as $sensor_id => $sensor_data) {
                                    $data["s{$sensor_id}"] = $sensor_data['v'];
                                }
                            }

                            return $data;

                        }, $snapToRoad ? snapToRoad($positions) : $positions),
                ];

                if ($status == self::STATUS_STOP) {
                    $item['lat'] = $group->getStartPosition()->latitude;
                    $item['lng'] = $group->getStartPosition()->longitude;
                }

                $items[] = $item;
            }

            foreach ($events as $event) {
                $items[] = $this->getEventPosition($event);
            }

            $items[] = [
                'status'   => self::STATUS_END,
                'icon'      => $this->icons[self::STATUS_END] ?? null,
                'lat'       => $data['root']->getEndPosition()->latitude,
                'lng'       => $data['root']->getEndPosition()->longitude,
                'start'     => $this->getPositionLocation($data['root']->getEndPosition()),
                'metas'     => $this->getGroupMetas($data['root'], [
                    'driver',
                    'distance',
                    'drive_duration',
                    'stop_duration',
                    'speed_max',
                    'fuel_consumption',
                    'engine_hours'
                ]),
            ];
        }

        $sensors = [
            [
                'key' => 's',
                'name' => trans('front.speed'),
                'unit' => Formatter::speed()->unit()
            ],
            [
                'key' => 'a',
                'name' => trans('front.altitude'),
                'unit' => Formatter::altitude()->unit()
            ]
        ];

        $sensors = array_merge($sensors, $this->getDeviceSensors($device)->map(
            function ($sensor) {
                return [
                    'key'  => "s{$sensor->id}",
                    'name' => $sensor->formatName(),
                    'unit' => $sensor->getUnit(),
                ];
            })->toArray());

        return [
            'items' => $items,
            'sensors' => $sensors,

            'classes' => [
                self::STATUS_DRIVE => [
                    'tr' => 'drive-action',
                    'class' => 'action-icon blue',
                    'sym' => 'D'
                ],
                self::STATUS_STOP => [
                    'tr' => 'park-action',
                    'class' => 'action-icon grey',
                    'sym' => 'P'
                ],
                self::STATUS_START => [
                    'tr' => '',
                    'class' => 'action-icon white',
                    'sym' => '<i class="fa fa-arrow-down"></i>'
                ],
                self::STATUS_END => [
                    'tr' => '',
                    'class' => 'action-icon white',
                    'sym' => '<i class="fa fa-flag-o"></i>'
                ],
                self::STATUS_EVENT => [
                    'tr' => 'event-action',
                    'class' => 'action-icon red',
                    'sym' => 'E'
                ]
            ]
        ];
    }

    public function getApi() {

        HistoryFormValidator::validate('create', $this->data);

        $device = $this->getDevice();

        $data = $this->getHistoryData($device);

        $deviceSensors = $this->getDeviceSensors($device);

        $items = [];

        if ($data['root']->getStartPosition()) {

            $events = EventRepo::search(['device_id' => $device->id, 'user_id' => $this->user->id] + $this->data);

            $items[] = [
                'status'   => self::STATUS_START,
                'time'     => null,
                'show'     => $data['root']->getStartAt(),
                'raw_time' => Formatter::time()->convert($data['root']->getStartPosition()->time),
                'distance' => 0,
                'driver'   => null,
                'items'    => [
                    $this->positionToItem($data['root']->getStartPosition())
                ],
            ];

            foreach ($data['groups']->all() as $group) {
                $status = $group->getKey() == 'stop' ? self::STATUS_STOP : self::STATUS_DRIVE;

                $drivers = $group->getStat('drivers')->get();
                $driver = empty($drivers) ? null : runCacheEntity(UserDriver::class, $drivers[0])->first();

                while ($event = Arr::first($events)) {
                    if (strtotime($group->getStartPosition()->time) < strtotime($event['time'])) {
                        break;
                    }

                    array_shift($events);

                    $items[] = [
                        'status'   => self::STATUS_EVENT,
                        'time'     => null,
                        'distance' => 0,
                        'show'     => Formatter::time()->human($event['time']),
                        'raw_time' => Formatter::time()->convert($event['time']),
                        'message'  => $event['message'],
                        'items'    => [
                            [
                                'other'    => '',
                                'speed'    => Formatter::speed()->format($event['speed']),
                                'time'     => Formatter::time()->human($event['time']),
                                'raw_time' => Formatter::time()->convert($event['time']),
                                'lat'      => strval($event['latitude']),
                                'lng'      => strval($event['longitude'])
                            ]
                        ]
                    ];
                }

                $items[] = [
                    'status'           => $status,
                    'time'             => $group->getStat('duration')->human(),
                    'show'             => $group->getStartAt(),
                    'left'             => $group->getEndAt(),
                    'raw_time'         => Formatter::time()->convert($group->getStartPosition()->time),
                    'distance'         => $group->getStat('distance')->format(),
                    'time_seconds'     => $group->getStat('duration')->value(),
                    'engine_work'      => intval($group->getStat('engine_work')->value()),
                    'engine_idle'      => intval($group->getStat('engine_idle')->value()),
                    'engine_hours'     => intval($group->getStat('engine_hours')->value()),
                    'fuel_consumption' => $group->hasStat('fuel_consumption') ? (int)$group->getStat('fuel_consumption')->format() : null,
                    'top_speed'        => $group->getStat('speed_max')->format(),
                    'average_speed'    => $group->getStat('speed_avg')->format(),
                    'driver'           => $driver,
                    'items'            => array_map(
                        function ($position) {
                            return $this->positionToItem($position);
                        }, $group->getStat('positions')->value()),
                ];
            }

            foreach ($events as $event) {
                $items[] = [
                    'status'   => self::STATUS_EVENT,
                    'time'     => null,
                    'distance' => 0,
                    'show'     => Formatter::time()->human($event['time']),
                    'raw_time' => Formatter::time()->convert($event['time']),
                    'message'  => $event['message'],
                    'items'    => [
                        [
                            'other'    => '',
                            'speed'    => Formatter::speed()->format($event['speed']),
                            'time'     => Formatter::time()->human($event['time']),
                            'raw_time' => Formatter::time()->convert($event['time']),
                            'lat'      => strval($event['latitude']),
                            'lng'      => strval($event['longitude'])
                        ]
                    ]
                ];
            }

            $items[] = [
                'status'   => self::STATUS_END,
                'time'     => null,
                'show'     => $data['root']->getEndAt(),
                'raw_time' => Formatter::time()->convert($data['root']->getEndPosition()->time),
                'distance' => 0,
                'driver'   => null,
                'items'    => [
                    $this->positionToItem($data['root']->getEndPosition())
                ],
            ];
        }

        $device = $device->toArray();
        unset($device['users']);

        $sensors = [
            [
                'id' => 'speed',
                'name' => trans('front.speed'),
                'sufix' => Formatter::speed()->unit()
            ],
            [
                'id' => 'altitude',
                'name' => trans('front.altitude'),
                'sufix' => Formatter::altitude()->unit()
            ]
        ];

        $sensors = array_merge($sensors, $deviceSensors->map(function($sensor) {
            return [
                'id' => 'sensor_'.$sensor['id'],
                'name' => $sensor->formatName(),
                'sufix' => $sensor['unit_of_measurement']
            ];
        })->values()->all());

        return [
            'items' => $items,
            'device' => $device,
            'distance_sum' => $data['root']->getStat('distance')->human(),
            'top_speed' => $data['root']->getStat('speed_max')->human(),
            'move_duration' => $data['root']->getStat('drive_duration')->human(),
            'stop_duration' => $data['root']->getStat('stop_duration')->human(),
            'fuel_consumption' => $data['root']->hasStat('fuel_consumption') ? $data['root']->getStat('fuel_consumption')->human() : null,
            'sensors' => $sensors,
            'fuel_consumption_arr' => [],
            'item_class' => [
                [
                    'id' => self::STATUS_DRIVE,
                    'value' => 'drive',
                    'title' => trans('front.drive')
                ],
                [
                    'id' => self::STATUS_STOP,
                    'value' => 'stop',
                    'title' => trans('front.stop')
                ],
                [
                    'id' => self::STATUS_START,
                    'value' => 'start',
                    'title' => trans('front.start')
                ],
                [
                    'id' => self::STATUS_END,
                    'value' => 'end',
                    'title' => trans('front.end')
                ],
                [
                    'id' => self::STATUS_EVENT,
                    'value' => 'event',
                    'title' => trans('front.event')
                ],
            ],
            'images' => apiArray([
                self::STATUS_DRIVE => asset('assets/images/route_drive.png'),
                self::STATUS_STOP => asset('assets/images/route_stop.png'),
                self::STATUS_START => asset('assets/images/route_start.png'),
                self::STATUS_END => asset('assets/images/route_end.png'),
                self::STATUS_EVENT => asset('assets/images/route_event.png')
            ]),
        ];
    }

    public function getMessages()
    {
        $validator = Validator::make($this->data, [
            'device_id' => 'required',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        $device = DeviceRepo::find($this->data['device_id']);
        $sorting = isset($this->data['sorting']) && $this->data['sorting'] == 'true' ? 'desc' : 'asc';
        $sorting = isset($this->data['sorting']['sort']) && $this->data['sorting']['sort'] == 'desc' ? 'desc' : $sorting;

        $this->checkException('history', 'view');
        $this->checkException('devices', 'own', $device);

        $pagination_limits = [
            '50' => 50,
            '100' => 100,
            '200' => 200,
            '300' => 300,
            '400' => 400,
            '500' => 500,
            '1000' => 1000,
        ];

        if (empty($this->data['limit']) || empty($pagination_limits[$this->data['limit']]))
            $this->data['limit'] = 50;

        $limit = $this->data['limit'];

        $parameters = [];

        $sensors = $device->sensors->filter(function($sensor){
            return $sensor['show_in_popup'] || $sensor['add_to_history'];
        })->values()->all();

        try {
            $messages = $device->positions()
                ->whereBetween('time', [
                    Formatter::time()->reverse($this->data['from_date'] . ' ' . $this->data['from_time']),
                    Formatter::time()->reverse($this->data['to_date'] . ' ' . $this->data['to_time'])
                ])
                ->orderBy('time', $sorting)
                ->orderBy('id', $sorting)
                ->paginate($limit)
                ->appends(['limit' => $limit]);
        } catch (QueryException $e) {
            $messages = collect();
        }

        foreach ($messages as $key => &$message) {
            $message->device_id = $device->id;
            $message->time = Formatter::time()->human($message->time);
            $message->server_time = Formatter::time()->human($message->server_time);
            $message->speed = Formatter::speed()->format($device->getSpeed($message));
            $message->altitude = Formatter::altitude()->format($message->altitude);

            $message->other_arr = empty($message->other) ? [] : parseXML($message->other);

            $message->other_array = parseXMLToArray($message->other);
            foreach($message->other_array as $el => $oval) {
                if (array_key_exists($el, $parameters) || empty($el))
                    continue;

                $parameters[$el] = '';
            }

            $popup_sensors = [];
            $sensors_value = [];

            foreach ($sensors as $sensor) {
                $sensors_value[$sensor['id']] = $sensor->getValueFormated($message->other, false);

                if ($sensor['show_in_popup']) {
                    $popup_sensors[$sensor['id']] = [
                        'name' => $sensor->formatName(),
                        'value' => $sensors_value[$sensor['id']]]
                    ;
                }
            }

            $message->popup_sensors = $popup_sensors;
            $message->sensors_value = $sensors_value;

            //for API dirty fix
            $message->sensors_values = "";
        }

        if (!isset($this->data['page']))
            $this->data['page'] = 1;

        if ($this->api) {
            $messages = $messages->toArray();
            $messages['url'] = route('api.get_history_messages');
        }

        return compact('messages', 'sensors', 'pagination_limits', 'limit', 'sorting', 'parameters');
    }

    public function getPosition()
    {
        $validator = Validator::make($this->data, [
            'device_id' => 'required',
            'position_id' => 'required',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        $device = DeviceRepo::find($this->data['device_id']);

        $this->checkException('history', 'view');
        $this->checkException('devices', 'own', $device);

        $position = $device->positions()->find($this->data['position_id']);

        if (empty($position))
            throw new ResourseNotFoundException('front.position');
        
        $time = $position->time;
        $position->lat = $position->latitude;
        $position->lng = $position->longitude;
        $position->time = Formatter::time()->human($time);
        $position->position_id = $position->id;

        $position->speed = Formatter::speed()->format($device->getSpeed($position));
        $position->altitude = Formatter::altitude()->format($position->altitude);
        $position->distance = Formatter::distance()->format($position->distance);
        $position->other_arr = empty($position->other) ? [] : parseXML($position->other);

        $sensors_value = [];
        foreach ($device['sensors'] as $sensor) {
            if (!$sensor['show_in_popup'])
                continue;

            $sensors_value[$sensor['id']] = $sensor->getValueFormated($position->other);
        }

        $position->sensors_value = $sensors_value;


        if (config('addon.distance_point')){
            if ($this->data['position_start_id']){
                
                $position_start = $device->positions()->find($this->data['position_start_id']);
            
                $distance_from_start = 0;
                $messages = $device->positions()
                    ->whereBetween('id', [$this->data['position_start_id'],$this->data['position_id']])
                    ->orderBy('id', 'ASC')->get();
                
                foreach ($messages as $pos){
                    $distance_from_start+=$pos->distance;
                }
                $duration = strtotime($time) - strtotime($position_start->time);
                $position->distance_from_start = Formatter::distance()->format($distance_from_start);
                $position->time_from_start = $position_start->time;
                $position->duration_from_start = Formatter::duration()->human($duration);
            }
        }

        return compact('position');
    }

    public function deletePositions()
    {
        $this->checkException('history', 'remove');

        $device = DeviceRepo::find($this->data['device_id']);

        $this->checkException('devices', 'own', $device);

        $ids = [];
        $ids = empty($this->data['id']) ? $ids : $this->data['id'];
        $ids = empty($this->data['ids']) ? $ids : $this->data['ids'];

        if ($ids)
        {
            if ( ! is_array($ids))
                $ids = [$ids];

            $device->positions()->whereIn('id', $ids)->delete();
            $device->generateTail();
        }

        return $this->api ? ['status' => 1] : response()->json(['status' => 1]);
    }

    protected function positionToItem($position) {

        $sensor_data = [
            [
                'id' => 'speed',
                'value' => (float)$position->speed
            ],
            [
                'id' => 'altitude',
                'value' => (float)$position->altitude
            ]
        ];

        if (!empty($position->sensors))
            foreach ($position->sensors as $sensor_id => $value)
                $sensor_data[] = [
                    'id' => "sensor_$sensor_id",
                    'value' => (float)$value['v'],
                ];

        return [
            'id' => $position->id,
            'device_id' => $position->device_id ?? null,
            'item_id' => 'i' . $position->id,
            'time' => Formatter::time()->human($position->time),
            'raw_time' => Formatter::time()->convert($position->time),
            'altitude' => $position->altitude,
            'course' => $position->course,
            'speed' => (int)$position->speed,
            'latitude' => $position->latitude,
            'longitude' => $position->longitude,
            'lat' => $position->latitude,
            'lng' => $position->longitude,
            'distance' => $position->distance ?? 0,
            'other' => $position->other,
            'color' => $position->color ?? null,

            'valid' => $position->valid,
            'device_time' => $position->device_time,
            'server_time' => $position->server_time,

            'other_arr' => parseXML($position->other),
            'sensors_data' => $sensor_data,
        ];
    }

    protected function getGroupMetas($group, $keys)
    {
        $metas = [];

        foreach ($keys as $key) {
            switch ($key) {
                case 'altitude':
                    $metas['altitude'] = [
                        'title' => trans('front.altitude'),
                        'value' => Formatter::altitude()->human($group->getStartPosition()->altitude)
                    ];

                    break;
                case 'speed':
                    $metas['speed'] = [
                        'title' => trans('front.speed'),
                        'value' => Formatter::speed()->human($group->getStartPosition()->speed)
                    ];

                    break;
                case 'speed_max':
                    $metas['speed_max'] = [
                        'title' => trans('front.top_speed'),
                        'value' => $group->getStat('speed_max')->human()
                    ];

                    break;
                case 'speed_avg':
                    $metas['speed_avg'] = [
                        'title' => trans('front.average_speed'),
                        'value' => $group->getStat('speed_avg')->human()
                    ];

                    break;
                case 'duration':
                    $metas['duration'] = [
                        'title' => trans('front.duration'),
                        'value' => $group->getStat('duration')->human()
                    ];

                    break;
                case 'distance':
                    $metas['distance'] = [
                        'title' => trans('front.route_length'),
                        'value' => $group->getStat('distance')->human()
                    ];

                    break;
                case 'driver':
                    $drivers = $group->getStat('drivers')->get();
                    $driver = empty($drivers) ? '-' : runCacheEntity(UserDriver::class, $drivers)->implode('name', ', ');

                    $metas['driver'] = [
                        'title' => trans('front.driver'),
                        'value' => $driver
                    ];

                    break;
                case 'drive_duration':
                    $metas['drive_duration'] = [
                        'title' => trans('front.move_duration'),
                        'value' => $group->getStat('drive_duration')->human()
                    ];
                    break;
                case 'stop_duration':
                    $metas['stop_duration'] = [
                        'title' => trans('front.stop_duration'),
                        'value' => $group->getStat('stop_duration')->human()
                    ];

                    break;
                case 'fuel_consumption':
                    if ($group->hasStat('fuel_consumption')) {
                        $stats = $group->stats()->like('fuel_consumption_');

                        foreach ($stats as $stat_key => $stat) {
                            $metas[$stat_key] = [
                                'title' => trans('front.fuel_consumption') . " ({$stat->getName()})",
                                'value' => $stat->human()
                            ];
                        }
                    }
                    break;
                case 'engine_hours':
                    if ($group->hasStat('engine_hours')) {
                        $metas['engine_hours'] = [
                            'title' => trans('front.engine_hours'),
                            'value' => $group->getStat('engine_hours')->human()
                        ];
                    }
                    break;
            }
        }

        return $metas;
    }

    protected function getPositionLocation($position)
    {
        return [
            'datetime'  => Formatter::time()->human($position->time),
            'date'      => Formatter::date()->convert($position->time),
            'time'      => Formatter::dtime()->convert($position->time),
            'lat'       => $position->latitude,
            'lng'       => $position->longitude,
        ];
    }

    protected function getEventPosition($event)
    {
        return [
            'status'   => self::STATUS_EVENT,
            'icon'     => $this->icons[self::STATUS_EVENT] ?? null,
            'message'  => $event['message'],
            'lat'      => strval($event['latitude']),
            'lng'      => strval($event['longitude']),
            'start'    => [
                'datetime'  => Formatter::time()->human($event['time']),
                'date'      => Formatter::date()->convert($event['time']),
                'time'      => Formatter::dtime()->convert($event['time']),
                'lat'       => strval($event['latitude']),
                'lng'       => strval($event['longitude']),
            ],
            'metas'     => [
                'message' => [
                    'title' => trans('front.event'),
                    'value' => $event['message']
                ],
            ],
        ];
    }
}