<?php namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupOverspeed;
use Tobuli\History\Actions\GSM;
use Tobuli\History\Actions\Harsh;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Seatbelt;
use Tobuli\History\Actions\Speed;
use Formatter;
use Tobuli\Reports\DeviceHistoryReport;

class OverspeedCustomReport extends DeviceHistoryReport
{
    private $cache = [];

    const TYPE_ID = 33;

    protected $disableFields = ['geofences', 'stops'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.overspeed_custom');
    }

    public static function isEnabled()
    {
        return settings('plugins.overspeed_custom_report.status');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            Speed::class,
            OverspeedStatic::class,
            Harsh::class,
            Seatbelt::class,
            GSM::class,

            GroupOverspeed::class,
        ];
    }

    protected function defaultMetas()
    {
        return array_merge(parent::defaultMetas(), [
            'device.plate_number' => trans('validation.attributes.plate_number'),
            'device.registration_number' => trans('validation.attributes.registration_number'),
        ]);
    }


    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group) {
            $rows[] = $this->getDataFromGroup($group, [
                    'start_at',
                    'end_at',
                    'duration',
                    'speed_max',
                    'speed_avg',
                    'location',
                    'distance',
                    'address',
                    'date',
                    'harsh_breaking_count',
                    'harsh_acceleration_count',
                    'seatbelt_off_duration',
                    'gsm',
                ]) + [
                    'course' => Formatter::course()->human($group->getStartPosition()->course),
                ];
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function getDeviceHistoryData($device)
    {
        $alert = $device->alerts()
            ->where('alerts.user_id', $this->user->id)
            ->where('type', 'overspeed')
            ->first();

        $speed_limit = ( ! is_null($alert)) ? (int)$alert->overspeed : $this->cache['speed_limit'];

        if (!$speed_limit) return null;

        $this->setSpeedLimit($speed_limit);

        return parent::getDeviceHistoryData($device);
    }

    protected function beforeGenerate()
    {
        $this->cache['speed_limit'] = $this->getSpeedLimit();
    }
}