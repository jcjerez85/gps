<?php

namespace Tobuli\Protocols;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Tobuli\InputFields\AbstractField;
use CustomFacades\Field;

class Commands
{
    const TYPE_CUSTOM = 'custom';
    const TYPE_IDENTIFICATION = "deviceIdentification";
    const TYPE_POSITION_SINGLE = "positionSingle";
    const TYPE_POSITION_PERIODIC = "positionPeriodic";
    const TYPE_POSITION_LOG = "positionLog";
    const TYPE_POSITION_STOP = "positionStop";
    const TYPE_ENGINE_STOP = "engineStop";
    const TYPE_ENGINE_RESUME = "engineResume";
    const TYPE_ALARM_ARM = "alarmArm";
    const TYPE_ALARM_DISARM = "alarmDisarm";
    const TYPE_SET_TIMEZONE = "setTimezone";
    const TYPE_REQUEST_PHOTO = "requestPhoto";
    const TYPE_REQUEST_VIDEO = "requestVideo";
    const TYPE_REBOOT_DEVICE = "rebootDevice";
    const TYPE_SEND_SMS = "sendSms";
    const TYPE_SEND_USSD = "sendUssd";
    const TYPE_SOS_NUMBER = "sosNumber";
    const TYPE_SILENCE_TIME = "silenceTime";
    const TYPE_SET_PHONEBOOK = "setPhonebook";
    const TYPE_VOICE_MESSAGE = "voiceMessage";
    const TYPE_OUTPUT_CONTROL = "outputControl";
    const TYPE_VOICE_MONITORING = "voiceMonitoring";
    const TYPE_SET_AGPS = "setAgps";
    const TYPE_SET_INDICATOR = "setIndicator";
    const TYPE_CONFIGURATION = "configuration";
    const TYPE_GET_VERSION = "getVersion";
    const TYPE_FIRMWARE_UPDATE = "firmwareUpdate";
    const TYPE_SET_CONNECTION = "setConnection";
    const TYPE_SET_ODOMETER = "setOdometer";

    const TYPE_DOOR_OPEN = "doorOpen";
    const TYPE_DOOR_CLOSE = "doorClose";
    const TYPE_TEMPLATE = "template";
    
    const TYPE_MODE_POWER_SAVING = "modePowerSaving";
    const TYPE_MODE_DEEP_SLEEP = "modeDeepSleep";
    
    const TYPE_ALARM_GEOFENCE = "movementAlarm";
    const TYPE_ALARM_BATTERY = "alarmBattery";
    const TYPE_ALARM_SOS = "alarmSos";
    const TYPE_ALARM_REMOVE = "alarmRemove";
    const TYPE_ALARM_CLOCK = "alarmClock";
    const TYPE_ALARM_SPEED = "alarmSpeed";
    const TYPE_ALARM_FALL = "alarmFall";
    const TYPE_ALARM_VIBRATION = "alarmVibration";
    
    const KEY_UNIQUE_ID = "uniqueId";
    const KEY_FREQUENCY = "frequency";
    const KEY_TIMEZONE = "timezone";
    const KEY_DEVICE_PASSWORD = "devicePassword";
    const KEY_RADIUS = "radius";
    const KEY_MESSAGE = "message";
    const KEY_ENABLE = "enable";
    const KEY_DATA = "data";
    const KEY_INDEX = "index";
    const KEY_PHONE = "phone";
    const KEY_SERVER = "server";
    const KEY_PORT = "port";

    const KEY_UNIT = "unit";
    const KEY_TYPE = "type";

    protected $commands = [];

    public function __construct()
    {
        $this->commands = [
            self::TYPE_CUSTOM => [
                'type' => self::TYPE_CUSTOM,
                'title' => trans('front.custom_command'),
                'attributes' => collect([
                    Field::text(self::KEY_DATA, trans('validation.attributes.message'))
                        ->setRequired()
                        ->setDescription(trans('front.raw_command_supports') .'<br><br>'. trans('front.gprs_template_variables')),
                ])
            ],
            self::TYPE_GET_VERSION => [
                'type' => self::TYPE_GET_VERSION,
                'title' => trans('front.get_version')
            ],
            self::TYPE_POSITION_SINGLE => [
                'type' => self::TYPE_POSITION_SINGLE,
                'title' => trans('front.position_single'),
            ],
            self::TYPE_POSITION_STOP => [
                'type' => self::TYPE_POSITION_STOP,
                'title' => trans('front.stop_reporting'),
            ],
            self::TYPE_POSITION_PERIODIC => [
                'type' => self::TYPE_POSITION_PERIODIC,
                'title' => trans('front.periodic_reporting'),
                'attributes' => collect([
                    Field::select(self::KEY_UNIT, trans('validation.attributes.unit'), 'minute')
                        ->setOptions([
                            'second' => trans('front.second'),
                            'minute' => trans('front.minute'),
                            'hour' => trans('front.hour'),
                        ])
                        ->setRequired()
                        ->addValidation('in:second,minute,hour'),
                    Field::number(self::KEY_FREQUENCY, trans('validation.attributes.frequency'), 1)
                        ->setRequired()
                        ->addValidation('numeric'),
                ])
            ],
            self::TYPE_POSITION_LOG => [
                'type' => self::TYPE_POSITION_LOG,
                'title' => trans('front.setting_log_interval'),
                'attributes' => collect([
                    Field::select(self::KEY_UNIT, trans('validation.attributes.unit'), 'minute')
                        ->setOptions([
                            'second' => trans('front.second'),
                            'minute' => trans('front.minute'),
                            'hour' => trans('front.hour'),
                        ])
                        ->setRequired()
                        ->addValidation('in:second,minute,hour'),
                    Field::number(self::KEY_FREQUENCY, trans('validation.attributes.frequency'), 1)
                        ->setRequired()
                        ->addValidation('numeric'),
                ])
            ],
            self::TYPE_OUTPUT_CONTROL => [
                'type' => self::TYPE_OUTPUT_CONTROL,
                'title' => trans('front.output_control'),
                'attributes' => collect([
                    Field::string(self::KEY_INDEX, 'Index')
                        ->setRequired(),
                    Field::string(self::KEY_DATA, 'Data')
                        ->setRequired(),
                ])
            ],
            self::TYPE_SET_TIMEZONE => [
                'type' => self::TYPE_SET_TIMEZONE,
                'title' => trans('front.set_timezone'),
                'attributes' => collect([
                    Field::select(self::KEY_TIMEZONE, trans('validation.attributes.parameter'), 'GMT')
                        ->setOptions($this->getTimeZoneOptions())
                        ->setRequired(),
                ])
            ],
            self::TYPE_ALARM_SPEED => [
                'type' => self::TYPE_ALARM_SPEED,
                'title' => 'TYPE_ALARM_SPEED',
                'attributes' => collect([
                    Field::string(self::KEY_DATA, trans('validation.attributes.parameter'))
                        ->setRequired(),
                ])
            ],
            self::TYPE_ALARM_SOS => [
                'type' => self::TYPE_ALARM_SOS,
                'title' => trans('front.sos_message_alarm'),
                'attributes' => collect([
                    Field::select(self::KEY_ENABLE, trans('validation.attributes.parameter'))
                        ->setOptions([
                            0 => trans('front.off'),
                            1 => trans('front.on'),
                        ])
                        ->setRequired(),
                ])
            ],
            self::TYPE_ALARM_BATTERY => [
                'type' => self::TYPE_ALARM_BATTERY,
                'title' => trans('front.low_battery_alarm'),
                'attributes' => collect([
                    Field::select(self::KEY_ENABLE, trans('validation.attributes.parameter'))
                        ->setOptions([
                            0 => trans('front.off'),
                            1 => trans('front.on'),
                        ])
                        ->setRequired(),
                ])
            ],
            self::TYPE_ALARM_REMOVE => [
                'type' => self::TYPE_ALARM_REMOVE,
                'title' => trans('front.alarm_of_taking_watch'),
                'attributes' => collect([
                    Field::select(self::KEY_ENABLE, trans('validation.attributes.parameter'))
                        ->setOptions([
                            0 => trans('front.off'),
                            1 => trans('front.on'),
                        ])
                        ->setRequired(),
                ])
            ],
            self::TYPE_ALARM_CLOCK => [
                'type' => self::TYPE_ALARM_CLOCK,
                'title' => trans('front.alarm_clock_setting_order'),
                'attributes' => collect([
                    Field::string(self::KEY_DATA, trans('validation.attributes.parameter'))
                        ->setRequired(),
                ])
            ],
            self::TYPE_ALARM_ARM => [
                'type' => self::TYPE_ALARM_ARM,
                'title' => trans('front.alarm_arm'),
            ],
            self::TYPE_ALARM_DISARM => [
                'type' => self::TYPE_ALARM_DISARM,
                'title' => trans('front.alarm_disarm'),
            ],
            self::TYPE_ALARM_GEOFENCE => [
                'type' => self::TYPE_ALARM_GEOFENCE,
                'title' => trans('front.movement_alarm'),
                'attributes' => collect([
                    Field::number(self::KEY_RADIUS, trans('validation.attributes.parameter'))
                        ->setRequired()
                        ->addValidation('integer'),
                ])
            ],
            self::TYPE_REQUEST_PHOTO => [
                'type' => self::TYPE_REQUEST_PHOTO,
                'title' => trans('front.request_photo')
            ],
            self::TYPE_REQUEST_VIDEO => [
                'type' => self::TYPE_REQUEST_VIDEO,
                'title' => trans('front.request_video')
            ],
            self::TYPE_ENGINE_STOP => [
                'type' => self::TYPE_ENGINE_STOP,
                'title' => trans('front.engine_stop')
            ],
            self::TYPE_ENGINE_RESUME => [
                'type' => self::TYPE_ENGINE_RESUME,
                'title' => trans('front.engine_resume')
            ],
            self::TYPE_REBOOT_DEVICE => [
                'type' => self::TYPE_REBOOT_DEVICE,
                'title' => trans('front.reboot_device')
            ],
            self::TYPE_DOOR_OPEN => [
                'type' => self::TYPE_DOOR_OPEN,
                'title' => trans('front.door_open')
            ],
            self::TYPE_DOOR_CLOSE => [
                'type' => self::TYPE_DOOR_CLOSE,
                'title' => trans('front.door_close')
            ],
            self::TYPE_SEND_SMS => [
                'type' => self::TYPE_SEND_SMS,
                'title' => trans('front.send_sms'),
                'attributes' => collect([
                    Field::string(self::KEY_PHONE, trans('validation.attributes.sim_number'))
                        ->setRequired(),
                    Field::text(self::KEY_MESSAGE, trans('validation.attributes.message'))
                        ->setRequired(),
                ])
            ],
            self::TYPE_SOS_NUMBER => [
                'type' => self::TYPE_SOS_NUMBER,
                'title' => trans('front.sos_number_setting'),
                'attributes' => collect([
                    Field::select(self::KEY_INDEX, self::KEY_INDEX, 1)
                        ->setOptions([
                            1 => trans('front.first'),
                            2 => trans('front.second'),
                            3 => trans('front.third'),
                        ])
                        ->setRequired(),
                    Field::string(self::KEY_PHONE, trans('validation.attributes.sim_number'))
                        ->setRequired(),
                ])
            ],
            self::TYPE_SILENCE_TIME => [
                'type' => self::TYPE_SILENCE_TIME,
                'title' => trans('front.time_interval_setting_of_silencetime'),
                'attributes' => collect([
                    Field::string(self::KEY_DATA, trans('validation.attributes.parameter'))
                        ->setRequired(),
                ])
            ],
            self::TYPE_SET_PHONEBOOK => [
                'type' => self::TYPE_SET_PHONEBOOK,
                'title' => trans('front.phone_book_setting_order'),
                'attributes' => $this->getSimNumberFields(5)
            ],

            self::TYPE_TEMPLATE => [
                'type' => self::TYPE_TEMPLATE,
                'title' => 'Template',
                'attributes' => collect([])
            ]
        ];
    }

    private function getTimeZoneOptions(): Collection
    {
        $zones = [];

        for ($i = -11; $i <= 11; $i++) {
            if ($i < 0) {
                $zone = "$i:00";
            } elseif ($i > 0) {
                $zone = "+$i:00";
            } else {
                $zone = '';
            }

            $zones["GMT$zone"] = "GMT $zone";
        }

        return collect($zones);
    }

    private function getSimNumberFields(int $amount): Collection
    {
        $fields = [];

        for ($i = 0; $i < $amount; $i++) {
            $no = $i + 1;

            $fields[] = Field::string('name', trans('validation.attributes.name') . ' ' . $no)
                ->setIndex($i)
                ->setValidation('');

            $fields[] = Field::string('phone', trans('validation.attributes.sim_number') . ' ' . $no)
                ->setIndex($i)
                ->setValidation('');
        }

        return collect($fields);
    }

    public function get($type, $attributes = [])
    {
        $command = $this->commands[$type];

        if ($attributes) {
            if (empty($command['attributes']))
                $command['attributes'] = collect([]);

            foreach ($attributes as $attribute)
                $command['attributes']->push($attribute);
        }

        return $command;
    }

    public function all()
    {
        return $this->commands;
    }

    public function only($only)
    {
        return Arr::only($this->commands, $only);
    }

    public static function validationRules($type, $commands)
    {
        $rules = [];

        foreach ($commands as $command)
        {
            if ($command['type'] != $type)
                continue;

            if (empty($command['attributes']))
                continue;

            /** @var AbstractField $attribute */
            foreach ($command['attributes'] as $attribute) {
                if ($validation = $attribute->getValidation()) {
                    $rules[$attribute->getName()] = $validation;
                }
            }
        }

        return $rules;
    }
}