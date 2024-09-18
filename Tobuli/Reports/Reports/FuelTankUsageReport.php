<?php

namespace Tobuli\Reports\Reports;

use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Device;
use Tobuli\Entities\UserDriver;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GroupRfid;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class FuelTankUsageReport extends DeviceHistoryReport
{
    const TYPE_ID = 75;

    protected $connectionsInfo = [];
    protected $actionGroup;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.fuel_tank_usage');
    }

    protected function getActionsList()
    {
        if ($this->actionGroup === 'tank') {
            return [
                GroupRfid::class,
                Fuel::class,
            ];
        }

        if ($this->actionGroup === 'vehicle') {
            return [
                Distance::class,
                Duration::class,
                EngineHours::class,
            ];
        }

        throw new \InvalidArgumentException('Unknown action group: ' . $this->actionGroup);
    }

    protected function generateDevice($device)
    {
        $this->getConnectionsInfo($device);
        $table = [];

        $this->actionGroup = 'tank';
        $tankData = $this->getDeviceHistoryData($device);
        $this->actionGroup = 'vehicle';

        $origDateFrom = $this->date_from;
        $origDateTo = $this->date_to;

        /** @var Group $group */
        foreach ($tankData['groups']->all() as $tankGroup) {
            if ($tankGroup->getKey() !== GroupRfid::KEY) {
                continue;
            }

            $rfid = $tankGroup->getStartPosition()->rfid;
            $this->date_from = $tankGroup->getStartPosition()->time;
            $this->date_to = $this->getRfidIntervalDateTo($rfid, $this->date_from);

            if ($result = $this->getTankAndVehicleStats($tankGroup)) {
                $table[] = $this->getRowMeta($rfid) + $result;
            }
        }

        $this->group->applyArray($tankData['root']->stats()->only(['fuel_consumption']));

        $this->date_from = $origDateFrom;
        $this->date_to = $origDateTo;

        return [
            'meta' => $this->getDeviceMeta($device),
            'table' => $table,
            'totals' => $this->getTotals($tankData['root'], ['fuel_consumption']),
        ];
    }

    /**
     * @throws \Exception
     */
    private function getTankAndVehicleStats(Group $tankGroup)
    {
        $fuel = $tankGroup->stats()->get('fuel_consumption')->value();

        if (!$fuel) {
            return null;
        }

        return $this->getDataFromGroup($tankGroup, ['fuel_consumption', 'start_at', 'end_at', 'location']);
    }

    private function getRfidIntervalDateTo(string $rfid, string $dateFrom)
    {
        foreach ($this->connectionsInfo as $info) {
            if ($rfid === $info->rfid && $info->date <= $dateFrom) {
                return $info->date_to;
            }
        }

        return null;
    }

    private function getConnectionsInfo(Device $tankDevice)
    {
        $this->connectionsInfo = [];

        $query = DB::query()
            ->select('p.date')
            ->from('user_driver_position_pivot AS p')
            ->where('p.device_id', $tankDevice->id);

        $info = (clone $query)
            ->addSelect('user_drivers.rfid')
            ->leftJoin('user_drivers', 'user_drivers.id', '=', 'p.driver_id')
            ->whereBetween('date', [$this->date_from, $this->date_to])
            ->orderBy('date')
            ->get()
            ->toArray();

        $lastItem = end($info);

        if ($lastItem === false) {
            return;
        }

        $last = (clone $query)
            ->where('date', '>', $lastItem->date)
            ->orderBy('date')
            ->first();

        for ($i = 0, $iMax = count($info) - 1; $i < $iMax; $i++) {
            $info[$i]->date_to = $info[$i + 1]->date;
        }

        $info[$iMax]->date_to = $last->date ?? date('Y-m-d H:i:s');

        $this->connectionsInfo = $info;
    }

    private function getRowMeta($rfid): array
    {
        /** @var UserDriver $driver */
        $driver = \Cache::store('array')->sear('rfid_driver_' . $rfid, function () use ($rfid) {
            return UserDriver::where('rfid', $rfid)->first();
        });

        return [
            'driver.name' => $driver->name ?? null,
            'driver.rfid' => $rfid,
        ];
    }

    public static function isEnabled()
    {
        return config('addon.report_fuel_tank_usage');
    }
}