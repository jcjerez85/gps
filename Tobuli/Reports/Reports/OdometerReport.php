<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\Odometer;
use Tobuli\Reports\DeviceHistoryReport;

class OdometerReport extends DeviceHistoryReport
{
    const TYPE_ID = 62;

    protected $disableFields = ['geofences', 'speed_limit', 'stops', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.odometer');
    }

    protected function getActionsList()
    {
        $list = [
            Duration::class,
            Distance::class,
            Odometer::class,
        ];

        return $list;
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        $odometer_start = trans('front.not_available');
        $odometer_end   = trans('front.not_available');
        $odometer = $data['root']->stats()->has('odometer') ? $data['root']->stats()->get('odometer') : null;

        if ($odometer) {
            $startPosition = $data['root']->getStartPosition();
            if ($startPosition && isset($startPosition->odometer)) {
                $odometer->set($startPosition->odometer);
                $odometer_start = round($odometer->format());
            }

            $endPosition = $data['root']->getEndPosition();
            if ($endPosition && isset($endPosition->odometer)) {
                $odometer->set($endPosition->odometer);
                $odometer_end = round($odometer->format());
            }
        }

        if (is_numeric($odometer_end) && is_numeric($odometer_start)) {
            $distance = $odometer_end - $odometer_start;
        } else {
            $distance = $data['root']->stats()->get('distance')->format();
        }

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'totals' => [
                'distance' => $distance,
                'odometer_start' => $odometer_start,
                'odometer_end' => $odometer_end,
            ]
        ];
    }
}