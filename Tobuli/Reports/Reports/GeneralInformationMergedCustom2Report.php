<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\Odometer;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationMergedCustom2Report extends DeviceHistoryReport
{
    const TYPE_ID = 42;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.general_information_merged_custom_2');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Odometer::class,
            OverspeedStatic::class,
        ];
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        $this->group->applyArray($data['root']->stats()->only([
            'distance',
            'drive_duration',
            'stop_duration',
            'stop_count',
            'overspeed_count',
            'odometer'
        ]));

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'totals' => $this->getDataFromGroup($data['root'], [
                'start_at',
                'end_at',
                'distance',
                'drive_duration',
                'stop_duration',
                'stop_count',
                'speed_max',
                'speed_avg',
                'overspeed_count',
                'odometer'
            ])
        ];
    }
}