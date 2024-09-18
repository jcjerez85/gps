<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\GeofencesCount;
use Tobuli\History\Actions\Odometer;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationMergedCustom5Report extends DeviceHistoryReport
{
    const TYPE_ID = 66;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.general_information_merged_custom_5');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            EngineHours::class,
            Odometer::class,
            GeofencesCount::class,
        ];
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        $this->group->applyArray($data['root']->stats()->only([
            'distance',
            'stop_duration',
            'stop_count',
            'engine_hours',
            'engine_idle',
            'engine_work',
            'geofences_out_count',
        ]));

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'totals' => $this->getDataFromGroup($data['root'], [
                'start_at',
                'end_at',
                'location_start',
                'location_end',
                'distance',
                'stop_duration',
                'stop_count',
                'engine_hours',
                'engine_idle',
                'engine_work',
                'geofences_out_count',
                'odometer',
            ])
        ];
    }
}