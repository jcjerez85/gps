<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Carbon\Carbon;
use Tobuli\Reports\DeviceReport;

class OfflineDeviceReport extends DeviceReport
{
    protected $offline_timeout;

    const TYPE_ID = 38;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.offline_objects');
    }

    public static function isEnabled() {
        return settings('plugins.offline_objects_report.status');
    }

    protected function beforeGenerate() {
        parent::beforeGenerate();

        $this->offline_timeout = settings('main_settings.default_object_online_timeout') * 60;

        $this->date_from = Carbon::now();
        $this->date_to   = Carbon::now();

        if (empty($this->devicesQuery)) {
            $this->setDevicesQuery($this->user->devices());
        }
    }

    protected function generateDevice($device)
    {
        $offline_duration = ($device->last_connect_timestamp != 0) ? time() - $device->last_connect_timestamp : 0;

        if ($offline_duration < $this->offline_timeout)
            return null;

        $odometer = null;
        $engine_hours = null;

        $odometer_sensor = $device->getOdometerSensor();
        $engine_hours_sensor = $device->getEngineHoursSensor();

        if ( ! is_null($odometer_sensor))
            $odometer = $odometer_sensor->getValue($device->other);

        if ( ! is_null($engine_hours_sensor))
            $engine_hours = $engine_hours_sensor->getValue($device->other);

        $distance = $device->getParameter('totaldistance') / 1000;
        $vEngineHours = $device->getParameter('enginehours');

        return [
            'meta' => $this->getDeviceMeta($device),
            'data' => [
                'time'             => Formatter::time()->human($device->lastConnectTime),
                'speed'            => Formatter::speed()->human($device->getSpeed()),
                'altitude'         => Formatter::altitude()->human($device->altitude),
                'course'           => $device->course,
                'offline_duration' => Formatter::duration()->human($offline_duration),
                'odometer'         => $odometer ? Formatter::distance()->human($odometer) : '',
                'engine_hours'     => $engine_hours ? Formatter::duration()->human($engine_hours) : '',
                'location'         => $this->getLocation($device, $this->getAddress($device)),
                'distance'         => Formatter::distance()->human($distance),
                'vEngineHours'     => Formatter::duration()->human($vEngineHours),
            ]
        ];
    }
}