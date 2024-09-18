<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DistanceBusiness;
use Tobuli\History\Actions\DistancePrivate;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupDriver;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class DriverDailyDistanceReport extends DeviceHistoryReport
{
    const TYPE_ID = 79;

    protected $disableFields = ['geofences', 'speed_limit', 'stops', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.driver_daily_distance');
    }

    protected function getActionsList()
    {
        return [
            Distance::class,
            DistancePrivate::class,
            DistanceBusiness::class,
            Drivers::class,
            GroupDriver::class,
            GroupDailySplit::class,
        ];
    }

    protected function generate()
    {
        $this->group = new Group('report');

        $this->getDevicesQuery()->chunk(1000, function ($devices) {
            foreach ($devices as $device) {
                $this->generateDevice($device);
            }
        });
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data)) {
            return;
        }

        $meta = $this->getDeviceMeta($device);

        /** @var Group $group */
        foreach ($data['groups']->all() as $group) {
            if (!$group->stats()->has('distance')) {
                continue;
            }

            $driver = $group->stats()->get('drivers');

            if ($this->getSkipBlankResults() && !$driver->value()) {
                continue;
            }

            $driverId = implode(',', $driver->value());
            $driverName = $driver->human();
            $distance = $group->stats()->get('distance');
            $distancePrivate = $group->stats()->get('distance_private');
            $distanceBusiness = $group->stats()->get('distance_business');
            $date = $group->getStartPosition()->date;

            $i = $this->getItemIndex($date, $driverId, $device->id);

            if ($i === false) {
                $this->items[] = [
                    'meta' => $meta + $this->getHistoryMeta($group),
                    'date' => $date,
                    'device_id' => $device->id,
                    'driver_id' => $driverId,
                    'driver_name' => $driverName,
                    'distance' => $distance,
                    'distance_business' => $distanceBusiness,
                    'distance_private' => $distancePrivate,
                ];
            } else {
                $this->items[$i]['distance']->apply($distance->value());
                $this->items[$i]['distance_business']->apply($distanceBusiness->value());
                $this->items[$i]['distance_private']->apply($distancePrivate->value());
                $this->items[$i]['meta'] = $meta + $this->getHistoryMeta($group);
            }
        }
    }

    private function getItemIndex(string $date, $driverId, $deviceId)
    {
        foreach ($this->items as $i => $item) {
            if ($item['date'] === $date
                && $item['driver_id'] === $driverId
                && $item['device_id'] === $deviceId
            ) {
                return $i;
            }
        }

        return false;
    }

    protected function afterGenerate()
    {
        array_walk($this->items, function (&$item) {
            $item['distance'] = $item['distance']->human();
            $item['distance_business'] = $item['distance_business']->human();
            $item['distance_private'] = $item['distance_private']->human();
        });
    }
}