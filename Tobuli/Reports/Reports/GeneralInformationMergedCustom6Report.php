<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Stats\StatAvg;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationMergedCustom6Report extends DeviceHistoryReport
{
    const TYPE_ID = 69;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.general_information_merged_custom_6');
    }

    public static function isEnabled()
    {
        return config('addon.general_information_6_report');
    }

    protected function getActionsList()
    {
        return [
            Drivers::class,
            EngineHours::class,
            Distance::class,
            Fuel::class,
        ];
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data)) {
            return null;
        }

        $this->group->applyArray($data['root']->stats()->only([
            'engine_hours',
            'distance',
            'fuel_consumption',
        ]));

        // add key to general group to force fuel avg total counting
        if (!$this->group->stats()->has('fuel_avg')) {
            $this->group->stats()->set('fuel_avg', new StatAvg());
        }

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'totals' => $this->getDataFromGroup($data['root'], [
                'drivers',
                'engine_hours',
                'distance',
                'fuel_avg',
                'fuel_consumption',
            ])
        ];
    }
}