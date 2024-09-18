<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Carbon\Carbon;
use Tobuli\Reports\DeviceReport;

class InstallationDeviceOfflineReport extends DeviceReport
{
    protected $offline_timeout;

    const TYPE_ID = 36;

    protected $enableFields = ['metas'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.installation_offline_objects');
    }

    public static function isEnabled() {
        return settings('plugins.additional_installation_fields.status');
    }

    protected function beforeGenerate()
    {
        parent::beforeGenerate();

        $this->date_from = Carbon::now();
        $this->date_to   = Carbon::now();

        $this->setDevicesQuery($this->user->devices()->offline());
    }

    protected function generateDevice($device)
    {
        $offline_duration = ($device->last_connect_timestamp != 0) ? time() - $device->last_connect_timestamp : 0;

        if ($offline_duration < $this->offline_timeout)
            return [
                'meta' => $this->getDeviceMeta($device),
                'error' => trans('front.nothing_found_request')
            ];

        return [
            'meta' => $this->getDeviceMeta($device),
            'data' => [
                'installation_date'   => $device->installation_date   != '0000-00-00' ? $device->installation_date : null,
                'sim_activation_date' => $device->sim_activation_date != '0000-00-00' ? $device->sim_activation_date : null,
                'sim_expiration_date' => $device->sim_expiration_date != '0000-00-00' ? $device->sim_expiration_date : null,
            ]
        ];
    }
}