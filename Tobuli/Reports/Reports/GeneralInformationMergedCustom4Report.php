<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationMergedCustom4Report extends DeviceHistoryReport
{
    const TYPE_ID = 56;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.general_information_merged_custom_4');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            EngineHours::class,
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
            'engine_work'
        ]));

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'totals' => $this->getDataFromGroup($data['root'], [
                'start_at',
                'end_at',
                'distance',
                'stop_duration',
                'stop_count',
                'engine_hours',
                'engine_idle',
                'engine_work'
            ])
        ];
    }
}