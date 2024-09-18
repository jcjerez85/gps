<?php namespace ModalHelpers;

use Formatter;
use CustomFacades\ModalHelpers\CustomEventModalHelper;
use CustomFacades\ModalHelpers\SendCommandModalHelper;
use CustomFacades\Repositories\AlertRepo;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Repositories\EventCustomRepo;
use CustomFacades\Repositories\PoiRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\AlertFormValidator;
use CustomFacades\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\TaskStatus;
use Tobuli\Exceptions\ValidationException;
use Tobuli\InputFields\AttributesCollection;
use Tobuli\Protocols\Commands;
use Tobuli\Services\AlertSoundService;
use Tobuli\Services\ScheduleService;


class AlertModalHelper extends ModalHelper
{
    protected $schedulesService;

    public function __construct()
    {
        parent::__construct();

        $this->schedulesService = new ScheduleService();
    }

    public function get()
    {
        try {
            $this->checkException('alerts', 'view');
        } catch (\Exception $e) {
            return ['alerts' => []];
        }

        if ($this->api) {
            $alerts = AlertRepo::getWithWhere(['devices', 'drivers', 'geofences', 'events_custom'], ['user_id' => $this->user->id]);
            $alerts = $alerts->toArray();

            foreach ($alerts as $key => $alert) {
                $drivers = [];
                foreach ($alert['drivers'] as $driver)
                    array_push($drivers, $driver['id']);

                $alerts[$key]['drivers'] = $drivers;

                $devices = [];
                foreach ($alert['devices'] as $device)
                    array_push($devices, $device['id']);

                $alerts[$key]['devices'] = $devices;

                $geofences = [];
                foreach ($alert['geofences'] as $geofence)
                    array_push($geofences, $geofence['id']);

                $alerts[$key]['geofences'] = $geofences;

                $events_custom = [];
                foreach ($alert['events_custom'] as $event)
                    array_push($events_custom, $event['id']);

                $alerts[$key]['events_custom'] = $events_custom;
            }
        } else {
            $alerts = AlertRepo::getWhere(['user_id' => $this->user->id]);
        }

        return compact('alerts');
    }

    public function createData()
    {
        $this->checkException('alerts', 'create');

        $devices = $this->user->devices;
        $geofences = Geofence::userAccessible($this->user)->pluck('name', 'id')->all();

        if (empty($devices))
            throw new ValidationException(['id' => trans('front.must_have_one_device')]);

        $types = $this->getTypesWithAttributes();
        $schedules = $this->schedulesService->getFormSchedules($this->user);
        $notifications = $this->getNotifications();

        $alert_zones = [
            '1' => trans('front.zone_in'),
            '2' => trans('front.zone_out')
        ];

        if ($this->api) {
            $devices = apiArray($devices->pluck('name', 'id')->all());
            $geofences = apiArray($geofences);
            $alert_zones = apiArray($alert_zones);
        } else {
            $devices = groupDevices($devices, $this->user);
        }

        return compact(
            'devices',
            'geofences',

            'types',
            'schedules',
            'notifications',
            'alert_zones'
        );
    }

    public function create()
    {
        $this->checkException('alerts', 'store');

        $this->validate('create');

        beginTransaction();
        try {
            $alert = $this->user->alerts()->create($this->data);

            $alert->devices()->sync(Arr::get($this->data, 'devices', []));
            $alert->geofences()->sync(Arr::get($this->data, 'geofences', []));
            $alert->drivers()->sync(Arr::get($this->data, 'drivers', []));
            $alert->zones()->sync(Arr::get($this->data, 'zones', []));
            $alert->pois()->sync(Arr::get($this->data, 'pois', []));

            $events_custom = Arr::get($this->data, 'events_custom', []);
            if ($events_custom) {
                $protocols = $alert->devices()->groupProtocols()->get()->pluck('protocol')->all();
                $events = EventCustomRepo::whereProtocols($events_custom, $protocols);
                $events_custom = $events->pluck('id')->all();
            }
            $alert->events_custom()->sync($events_custom);
        }
        catch (\Exception $e) {
            rollbackTransaction();
            throw $e;
        }

        commitTransaction();

        return ['status' => 1, 'item' => $alert];
    }

    public function editData()
    {
        $id = array_key_exists('alert_id', $this->data) ? $this->data['alert_id'] : request()->route('alerts');
        $id = $id ?: request()->route('id');

        $item = AlertRepo::findWithAttributes($id);

        $this->checkException('alerts', 'edit', $item);

        $devices = $this->user->devices;

        if (empty($devices))
            throw new ValidationException(['id' => trans('front.must_have_one_device')]);

        $types = $this->getTypesWithAttributes($item);
        $schedules = $this->schedulesService->setSchedules($item->schedules ?: [])->getFormSchedules($this->user);
        $notifications = $this->getNotifications($item);
        $commands = SendCommandModalHelper::getCommands($devices);
        $geofences = Geofence::userAccessible($this->user)->pluck('name', 'id')->all();

        $alert_zones = [
            '1' => trans('front.zone_in'),
            '2' => trans('front.zone_out')
        ];

        if ($this->api) {
            $devices     = apiArray($devices->pluck('name', 'id')->all());
            $geofences   = apiArray($geofences);
            $alert_zones = apiArray($alert_zones);
        } else {
            $devices = groupDevices($devices, $this->user);
        }

        return compact(
            'item',
            'devices',
            'geofences',

            'types',
            'schedules',
            'notifications',
            'alert_zones',
            'commands'
        );
    }

    public function edit()
    {
        $alert = AlertRepo::find($this->data['id']);

        $this->checkException('alerts', 'update', $alert);

        $this->validate('update');

        beginTransaction();
        try {
            AlertRepo::update($alert->id, $this->data);

            $alert->devices()->sync(Arr::get($this->data, 'devices', []));
            $alert->geofences()->sync(Arr::get($this->data, 'geofences', []));
            $alert->drivers()->sync(Arr::get($this->data, 'drivers', []));
            $alert->zones()->sync(Arr::get($this->data, 'zones', []));
            $alert->pois()->sync(Arr::get($this->data, 'pois', []));

            $events_custom = Arr::get($this->data, 'events_custom', []);
            if ($events_custom) {
                $protocols = $alert->devices()->groupProtocols()->get()->pluck('protocol')->all();
                $events = EventCustomRepo::whereProtocols($events_custom, $protocols);
                $events_custom = $events->pluck('id')->all();
            }
            $alert->events_custom()->sync($events_custom);
        }
        catch (\Exception $e) {
            rollbackTransaction();
            throw $e;
        }

        commitTransaction();

        return ['status' => 1];
    }

    private function validate($type)
    {
        $alert_id = Arr::get($this->data, 'id');

        AlertFormValidator::validate($type, $this->data, $alert_id);

        if ($schedules = $this->data['schedules'] ?? []) {
            $this->schedulesService->validate($schedules);
            $this->data['schedules'] = $this->schedulesService->setFormSchedules($schedules);
        }

        foreach (Arr::get($this->data, 'notifications', []) as $name => $notification)
        {
            $validator = null;
            $active = Arr::get($notification, 'active', false) ? true : false;

            switch ($name) {
                case 'silent':
                    if ($active) {
                        $validator = Validator::make($notification, [
                            'input' => 'required|integer|min:1',
                        ]);
                    }
                    break;
                case 'color':
                    if ($active) {
                        $notification['input'] = Arr::get($notification, 'input');
                        $validator = Validator::make($notification, [
                            'input'   => 'required|css_color',
                        ]);
                    }

                    break;
                case 'sound':
                case 'push':
                case 'auto_hide':
                    break;
                case 'email':
                    if ($active) {
                        $notification['input'] = semicol_explode(Arr::get($notification, 'input'));
                        $validator = Validator::make($notification, [
                            'input'   => 'required|array_max:'.config('tobuli.limits.alert_emails'),
                            'input.*' => 'email'
                        ]);
                    }

                    break;
                case 'webhook':
                    if ($active) {
                        $notification['input'] = semicol_explode(Arr::get($notification, 'input'));
                        $validator = Validator::make($notification, [
                            'input'   => 'required|array_max:' . config('tobuli.limits.alert_webhooks'),
                            'input.*' => 'curl_request'
                        ]);
                    }

                    break;
                case 'sms':
                    if ($active) {
                        $notification['input'] = semicol_explode(Arr::get($notification, 'input'));
                        $validator = Validator::make($notification, [
                            'input' => 'required|array_max:' . config('tobuli.limits.alert_phones')
                        ]);
                    }

                    break;
                case 'sharing_email':
                    if ($active) {
                        $notification['input'] = semicol_explode(Arr::get($notification, 'input'));
                        $validator = Validator::make($notification, [
                            'input' => 'required|array',
                            'input.*' => 'email'
                        ]);
                    }

                    break;
                case 'sharing_sms':
                    if ($active) {
                        $notification['input'] = semicol_explode(Arr::get($notification, 'input'));
                        $validator = Validator::make($notification, [
                            'input' => 'required|array'
                        ]);
                    }

                    break;
                default:
                    throw new ValidationException(["notifications.$name" => 'Notification type not supported.']);
            }

            if ($validator && $validator->fails())
                throw new ValidationException(["notifications.$name.input" => $validator->errors()->first()]);

            $this->data['notifications'][$name] = Arr::only($this->data['notifications'][$name], ['active', 'input']);
        }

        if (Arr::get($this->data, 'command.active'))
        {
            $devices = DeviceRepo::getWhereIn($this->data['devices']);
            $commands = SendCommandModalHelper::getCommands($devices);
            $rules = Commands::validationRules(Arr::get($this->data, 'command.type'), $commands);
            $validator = Validator::make($this->data, $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator->messages());
            }

            if ($rules) {
                $this->data['command'] = array_merge(
                    Arr::only($this->data, array_keys($rules)),
                    $this->data['command']
                );
            }
        }

    }

    public function doDestroy($id) {
        $item = AlertRepo::find($id);

        $this->checkException('alerts', 'remove', $item);

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('alert_id', $this->data) ? $this->data['alert_id'] : $this->data['id'];

        $this->checkException('alerts', 'clean');

        $ids = is_array($id) ? $id : [$id];

        $this->user->alerts()->whereIn('id', $ids)->delete();

        return ['status' => 1];
    }

    public function getTypesWithAttributes($alert = null)
    {
        $drivers = UserRepo::getDrivers($this->user->id)->pluck('name', 'id')->all();

        $geofences = Geofence::userAccessible($this->user)->pluck('name', 'id')->all();

        $pois = PoiRepo::whereUserId($this->user->id);
        $pois->map(function($item) {
            $item['title'] = $item['name'];
            return $item;
        })->only('id', 'title')->all();

        $events_custom = $alert ? CustomEventModalHelper::getGroupedEvents($alert->devices()) : [];
        $events_custom = Arr::pluck($events_custom, 'items');

        $types = self::getTypes();

        foreach ($types as & $type)
        {
            switch ($type['type']) {
                case 'overspeed':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'overspeed',
                            trans('validation.attributes.overspeed') . "(" . Formatter::speed()->getUnit() . ")",
                            $alert ? $alert->overspeed : ''
                        ),
                    ]);
                    break;
                case 'time_duration':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'time_duration',
                            trans('validation.attributes.time_duration') . '(' . trans('front.minutes') . ')',
                            $alert ? $alert->time_duration : ''
                        ),
                    ]);
                    break;
                case 'move_start':
                case 'stop_duration':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'stop_duration',
                            trans('validation.attributes.stop_duration_longer_than') . '(' . trans('front.minutes') . ')',
                            $alert ? $alert->stop_duration : ''
                        ),
                    ]);
                    break;
                case 'offline_duration':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'offline_duration',
                            trans('validation.attributes.offline_duration_longer_than') . '(' . trans('front.minutes') . ')',
                            $alert ? $alert->offline_duration : ''
                        ),
                    ]);
                    break;
                case 'idle_duration':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'idle_duration',
                            trans('validation.attributes.idle_duration_longer_than') . '(' . trans('front.minutes') . ')',
                            $alert ? $alert->idle_duration : ''
                        ),
                    ]);
                    break;
                case 'ignition_duration':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'ignition_duration',
                            trans('validation.attributes.ignition_duration_longer_than') . '(' . trans('front.minutes') . ')',
                            $alert ? $alert->ignition_duration : ''
                        ),
                    ]);

                    if ($this->user->perm('checklist', 'view')) {

                        $type['attributes']->push(
                            Field::select(
                                'pre_start_checklist_only',
                                trans('global.pre_start_checklist'),
                                $alert ? $alert->pre_start_checklist_only : 0
                            )->setOptions([
                                0 => trans('global.no'),
                                1 => trans('global.yes'),
                            ])->setDescription(trans('global.pre_start_checklist_alert_description'))
                        );
                    }
                    break;
                case 'ignition':
                    $type['attributes'] = AttributesCollection::make([
                        Field::select(
                            'state',
                            trans('validation.attributes.state'),
                            $alert ? $alert->state : null
                        )->setOptions([
                            0 => trans('global.all'),
                            1 => trans('front.on'),
                            2 => trans('front.off'),
                        ]),
                    ]);
                    break;
                case 'fuel_change':
                    $type['attributes'] = AttributesCollection::make([
                        Field::select(
                            'state',
                            trans('validation.attributes.state'),
                            $alert ? $alert->state : null
                        )->setOptions([
                            0 => trans('front.fill_theft'),
                            1 => trans('front.fill'),
                            2 => trans('front.theft'),
                        ]),
                    ]);
                    break;
                case 'driver':
                    $type['attributes'] = AttributesCollection::make([
                        Field::multiSelect(
                            'drivers',
                            trans('front.drivers'),
                            $alert ? $alert->drivers->pluck('id')->all() : []
                        )->setOptions($drivers),
                    ]);
                    break;
                case 'driver_unauthorized':
                    $type['attributes'] = AttributesCollection::make([
                        Field::select(
                            'authorized',
                            trans('validation.attributes.authorized'),
                            $alert ? $alert->authorized : '0'
                        )->setOptions([
                            0 => trans('global.no'),
                            1 => trans('global.yes'),
                        ]),
                    ]);
                    break;
                case 'geofence_in':
                case 'geofence_out':
                case 'geofence_inout':
                    $type['attributes'] = AttributesCollection::make([
                        Field::multiSelect(
                            'geofences',
                            trans('validation.attributes.geofences'),
                            $alert ? $alert->geofences->pluck('id')->all() : []
                        )->setOptions($geofences),
                    ]);
                    break;
                case 'custom':
                    $type['attributes'] = AttributesCollection::make([
                        Field::multiSelect(
                            'events_custom',
                            trans('validation.attributes.event'),
                            $alert ? $alert->events_custom->pluck('id')->all() : []
                        )->setOptions($events_custom)
                            ->setDescription(trans('front.alert_events_tip')),
                        Field::number(
                            'continuous_duration',
                            trans('validation.attributes.continuous_duration') . '(' . trans('front.second_short') . ')',
                            $alert ? $alert->continuous_duration : 0
                        ),
                    ]);
                    break;
                case 'distance':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'distance',
                            trans('validation.attributes.distance_limit') . '(' . Formatter::distance()->getUnit() . ')',
                            $alert ? $alert->distance : 0
                        ),
                        Field::number(
                            'period',
                            trans('validation.attributes.period') . '(' . trans('global.days') . ')',
                            $alert ? $alert->period : 0
                        ),
                    ]);
                    break;
                case 'poi_stop_duration':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'stop_duration',
                            trans('validation.attributes.stop_duration_longer_than') . ' (' . trans('front.minutes') . ')',
                            $alert ? $alert->stop_duration : ''
                        ),
                        Field::number(
                            'distance_tolerance',
                            trans('validation.attributes.distance_tolerance') . ' (' . trans('front.mt') . ')',
                            $alert ? $alert->distance_tolerance : ''
                        ),
                        $this->api
                        ? Field::multiSelect(
                            'pois',
                            trans('validation.attributes.pois'),
                            $alert ? $alert->pois->pluck('id')->all() : []
                        )->setOptions($pois->pluck('title', 'id')->all())
                        : Field::multiGroupSelect(
                            'pois',
                            trans('validation.attributes.pois'),
                            $alert ? $alert->pois->pluck('id')->all() : []
                        )->setOptions($pois)
                        ->setOptionsClosure('groupPois', [$this->user]),
                    ]);
                    break;
                case 'poi_idle_duration':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'idle_duration',
                            trans('validation.attributes.idle_duration_longer_than') . ' (' . trans('front.minutes') . ')',
                            $alert ? $alert->idle_duration : ''
                        ),
                        Field::number(
                            'distance_tolerance',
                            trans('validation.attributes.distance_tolerance') . ' (' . trans('front.mt') . ')',
                            $alert ? $alert->distance_tolerance : ''
                        ),
                        $this->api
                        ? Field::multiSelect(
                            'pois',
                            trans('validation.attributes.pois'),
                            $alert ? $alert->pois->pluck('id')->all() : []
                        )->setOptions($pois->pluck('title', 'id')->all())
                        : Field::multiGroupSelect(
                            'pois',
                            trans('validation.attributes.pois'),
                            $alert ? $alert->pois->pluck('id')->all() : []
                        )->setOptions($pois)
                        ->setOptionsClosure('groupPois', [$this->user]),
                    ]);
                    break;
                case 'task_status':
                    $type['attributes'] = AttributesCollection::make([
                        Field::multiSelect(
                            'statuses',
                            trans('validation.attributes.statuses'),
                            $alert ? $alert->statuses : []
                        )->setOptions(TaskStatus::getList())
                    ]);
                    break;
                case 'unplugged':
                    $type['attributes'] = AttributesCollection::make([
                        Field::number(
                            'continuous_duration',
                            trans('validation.attributes.continuous_duration') . '(' . trans('front.second_short') . ')',
                            $alert ? $alert->continuous_duration : 0
                        ),
                    ]);
                    break;
                default:
                    break;
            }
        }

        return $types;
    }

    public static function getTypes()
    {
        $types = [
            [
                'type'  => 'overspeed',
                'title' => trans('front.overspeed'),
            ],
            [
                'type'  => 'stop_duration',
                'title' => trans('front.stop_duration'),
            ],
            [
                'type'  => 'time_duration',
                'title' => trans('front.time_duration'),
            ],
            [
                'type'  => 'offline_duration',
                'title' => trans('front.offline_duration'),
            ],
            [
                'type' => 'ignition_duration',
                'title' => trans('front.ignition_duration'),
            ],
            [
                'type'  => 'idle_duration',
                'title' => trans('front.idle_duration'),
            ],
            [
                'type'  => 'ignition',
                'title' => trans('front.ignition_on_off'),
            ],
            [
                'type'  => 'move_start',
                'title' => trans('front.start_of_movement'),
            ],
            [
                'type'  => 'driver',
                'title' => trans('front.driver_change'),
            ],
            [
                'type'  => 'driver_unauthorized',
                'title' => trans('front.driver_change_authorization'),
            ],
            [
                'type'  => 'geofence_in',
                'title' => trans('front.geofence') . ' ' . trans('global.in'),
            ],
            [
                'type'  => 'geofence_out',
                'title' => trans('front.geofence') . ' ' . trans('global.out'),
            ],
            [
                'type'  => 'geofence_inout',
                'title' => trans('front.geofence') . ' ' . trans('global.in') . '/' . trans('global.out'),
            ],
            [
                'type'  => 'custom',
                'title' => trans('front.custom_events'),
            ],
            [
                'type'  => 'sos',
                'title' => 'SOS',
            ],
            [
                'type'  => 'fuel_change',
                'title' => trans('front.fuel') . ' (' . trans('front.fill_theft') . ')',
            ],
            [
                'type' => 'distance',
                'title' => trans('global.distance'),
            ],
            [
                'type'  => 'poi_stop_duration',
                'title' => trans('front.poi_stop_duration'),
            ],
            [
                'type'  => 'poi_idle_duration',
                'title' => trans('front.poi_idle_duration'),
            ],
            [
                'type'  => 'task_status',
                'title' => trans('front.task_status'),
            ],
        ];

        $expect = [];

        if (!config('addon.alert_time_duration'))
            $expect[] = 'time_duration';

        if (!auth()->user()->perm('tasks', 'view'))
            $expect[] = 'task_status';

        if (!empty($expect))
            $types = Arr::where($types, function ($type) use ($expect) {
                return !in_array($type['type'], $expect);
            });

        //reindex
        return array_values($types);
    }

    public function getNotifications($alert = null)
    {
        $alertNotifications = $alert->notifications ?? [];

        $notifications = [
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, 'color.active', false) : false,
                'name' => 'color',
                'title' => trans('validation.attributes.color'),
                'input_type' => 'color',
                'input' => $alertNotifications ? Arr::get($alertNotifications, 'color.input') : null,
            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "silent.active", false) : false,
                'name' => 'silent',
                'title' => trans('validation.attributes.silent_notification'),
                'input_type' => 'integer',
                'input' => $alertNotifications ? Arr::get($alertNotifications, "silent.input", '0') : '0',
            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "sound.active", true) : true,
                'name' => 'sound',
                'title' => trans('validation.attributes.sound_notification'),
                'input_type' => 'select',
                'input' => $alertNotifications ? Arr::get($alertNotifications, "sound.input", '') : '',
                'options' => toOptions(AlertSoundService::getList()),
            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "auto_hide.active", true) : true,
                'name' => 'auto_hide',
                'title' => trans('validation.attributes.auto_hide_notification'),

            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "push.active", true) : true,
                'name' => 'push',
                'title' => trans('validation.attributes.push_notification'),
            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "email.active", false) : false,
                'name' => 'email',
                'title' => trans('validation.attributes.email_notification'),
                'input_type' => 'string',
                'input' => $alertNotifications ? Arr::get($alertNotifications, "email.input", '') : '',
                'description' => trans('front.email_semicolon')
            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "sms.active", false) : false,
                'name' => 'sms',
                'title' => trans('validation.attributes.sms_notification'),
                'input_type' => 'string',
                'input' => $alertNotifications ? Arr::get($alertNotifications, "sms.input", '') : '',
                'description' => trans('front.sms_semicolon')
            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "webhook.active", false) : false,
                'name' => 'webhook',
                'title' => trans('validation.attributes.webhook_notification'),
                'input_type' => 'string',
                'input' => $alertNotifications ? Arr::get($alertNotifications, "webhook.input", '') : '',
                'description' => trans('front.webhook')
            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "sharing_email.active", false) : false,
                'name' => 'sharing_email',
                'title' => trans('validation.attributes.sharing_email'),
                'input_type' => 'string',
                'input' => $alertNotifications ? Arr::get($alertNotifications, "sharing_email.input", '') : '',
                'description' => trans('front.email_semicolon'),
            ],
            [
                'active' => $alertNotifications ? Arr::get($alertNotifications, "sharing_sms.active", false) : false,
                'name' => 'sharing_sms',
                'title' => trans('validation.attributes.sharing_sms'),
                'input_type' => 'string',
                'input' => $alertNotifications ? Arr::get($alertNotifications, "sharing_sms.input", '') : '',
                'description' => trans('front.sms_semicolon'),
            ],
        ];

        $notifications = Arr::where($notifications, function($notification) {
            if (in_array($notification['name'], ['sms', 'sharing_sms']) && ! auth()->user()->canSendSMS()) {
                return false;
            }

            if (in_array($notification['name'], ['sharing_email', 'sharing_sms'])
                && (! settings('plugins.alert_sharing.status')
                || ! auth()->user()->can('create', new \Tobuli\Entities\Sharing()))
            ) {
                return false;
            }

            return true;
        });

        // indexes reset with array_values
        return array_values($notifications);
    }

    public function getCommands()
    {
        AlertFormValidator::validate('commands', $this->data);

        $devices = DeviceRepo::getWhereIn($this->data['devices']);

        $commands = SendCommandModalHelper::getCommands($devices);
/*
        foreach ($commands as &$command) {
            if (empty($command['attributes']))
                continue;

            foreach($command['attributes'] as &$attribute) {
                $attribute['name'] = 'command[' .$attribute['name'] . ']';
            }
        }
*/
        return $commands;
    }

    public function syncDevices()
    {
        $alert = AlertRepo::find($this->data['alert_id']);

        $this->checkException('alerts', 'update', $alert);

        AlertFormValidator::validate('devices', $this->data);

        $alert->devices()->sync(Arr::get($this->data, 'devices', []));

        return ['status' => 1];
    }

    public function summary($from = null, $to = null)
    {
        $query = $this->user->alerts()
            ->select(DB::raw('count(*) as count, alerts.type'))
            ->join('events', 'alerts.id', '=', 'events.alert_id')
            ->groupBy('alerts.type');

        if ($from)
            $query->where('events.created_at', '>=', $from);

        if ($to)
            $query->where('events.created_at', '<=', $to);

        $alerts = $query->get()->pluck('count', 'type');

        $types = collect(AlertModalHelper::getTypes())
            ->map(function($type) use ($alerts) {
                $type['count'] = $alerts[$type['type']] ?? 0;

                return $type;
            });

        return $types;
    }
}