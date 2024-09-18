<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupSingle;
use Tobuli\History\Actions\Odometer;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class OdometerDailyReport extends DeviceHistoryReport
{
    const TYPE_ID = 78;

    protected $disableFields = ['geofences', 'speed_limit', 'stops', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.odometer_daily');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            Odometer::class,
            GroupSingle::class,
            GroupDailySplit::class,
        ];
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data)) {
            return null;
        }

        $table = [];

        /** @var Group $group */
        foreach ($data['groups']->all() as $group) {
            $table[] = $this->getRow($group);
        }

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'table' => $table,
            'totals' => $this->getRow($data['root']),
        ];
    }

    private function getRow(Group $group): array
    {
        $odometer_start = trans('front.not_available');
        $odometer_end = trans('front.not_available');
        $odometer = $group->stats()->has('odometer') ? $group->stats()->get('odometer') : null;

        $startPosition = $group->getStartPosition();
        $date = $startPosition->date ?? trans('front.not_available');

        if ($odometer) {
            if ($startPosition && isset($startPosition->odometer)) {
                $odometer->set($startPosition->odometer);
                $odometer_start = round($odometer->format());
            }

            $endPosition = $group->getEndPosition();
            if ($endPosition && isset($endPosition->odometer)) {
                $odometer->set($endPosition->odometer);
                $odometer_end = round($odometer->format());
            }
        }

        if (is_numeric($odometer_end) && is_numeric($odometer_start)) {
            $distance = $odometer_end - $odometer_start;
        } else {
            $distance = $group->stats()->get('distance')->format();
        }

        return compact('date', 'distance', 'odometer_start', 'odometer_end');
    }
}