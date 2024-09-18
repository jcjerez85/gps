<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationMergedReport extends DeviceHistoryReport
{
    const TYPE_ID = 2;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.general_information_merged');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            OverspeedStatic::class,
        ];
    }

    protected function generateDevice($device)
    {

        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'totals' => $this->getDataFromGroup($data['root'], [
                'start_at',
                'end_at',
                'distance',
                'drive_duration',
                'stop_duration',
                'speed_max',
                'speed_avg',
                'overspeed_count',
                'fuel_consumption',
                'fuel_price',
            ])
        ];
    }
}