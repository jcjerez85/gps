<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Device;
use Tobuli\Entities\UserDriver;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\FirstGroupRfidQuit;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Group;
use Tobuli\History\Stats\StatValue;
use Tobuli\Reports\DeviceHistoryReport;

class FuelTankUsageDriverReport extends DeviceHistoryReport
{
    const TYPE_ID = 76;

    protected $connectionsInfo = [];
    protected $actionGroup;

    /**
     * @var UserDriver
     */
    private $driver;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.fuel_tank_usage') . ' (' . trans('front.driver') . ')';
    }

    protected function getActionsList()
    {
        if ($this->actionGroup === 'tank') {
            return [
                FirstGroupRfidQuit::class,
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

    protected function precheckError($device)
    {
        $this->driver = UserDriver::where('name', $device->name)->first();

        if (!$this->driver) {
            return trans('global.not_found') . ' - ' . trans('front.driver') . ' ' . $device->name;
        }

        $this->getConnectionsInfo();

        if (count($this->connectionsInfo) === 0) {
            return trans('global.not_found') . ' - ' . trans('front.fuel_tank_usage');
        }

        return null;
    }

    protected function generateDevice($device)
    {
        if ($error = $this->precheckError($device)) {
            return [
                'meta' => $this->getDeviceMeta($device),
                'error' => $error
            ];
        }

        $table = [];

        $origDateFrom = $this->date_from;
        $origDateTo = $this->date_to;

        foreach ($this->connectionsInfo as $info) {
            $this->date_from = $info->date;
            $this->date_to = $info->date_to;

            $tankDevice = runCacheEntity(Device::class, $info->device_id)->first();
            $info->tank_data = $this->getTankData($tankDevice);
        }

        $totals = new Group('totals');
        $this->date_from = $this->getPreFirstDateFrom();

        foreach ($this->connectionsInfo as $info) {
            if (!isset($info->tank_data)) {
                continue;
            }

            $tankDevice = runCacheEntity(Device::class, $info->device_id)->first();

            $row = ['tank_name' => $tankDevice->name]
                + $this->getDataFromGroup($info->tank_data, [
                    'fuel_consumption', 'start_at', 'end_at', 'location'
                ]);

            $this->date_to = $info->date;

            $vehicleData = $this->getVehicleData($device, $info->tank_data);

            $this->date_from = $info->date;

            if ($vehicleData) {
                $row += $this->getDataFromGroup($vehicleData, [
                    'distance', 'engine_hours', 'fuel_h', 'fuel_100',
                ]);

                $totals->applyArray($vehicleData->stats()->only(['distance', 'engine_hours']));
            } else {
                $row['no_data'] = true;
            }

            $table[] = $row;
            $totals->applyArray($info->tank_data->stats()->only(['fuel_consumption']));
        }

        $fuel = $totals->stats()->has('fuel_consumption') ? (float)$totals->stats()->get('fuel_consumption')->value() : 0;

        $this->setFuelAvgDistance($totals, $fuel);
        $this->setFuelAvgEngine($totals, $fuel);

        $this->date_from = $origDateFrom;
        $this->date_to = $origDateTo;

        $this->group->applyArray($totals->stats()->only(['fuel_consumption', 'distance', 'engine_hours']));

        return [
            'meta' => $this->getDeviceMeta($device) + [[
                    'title' => trans('validation.attributes.rfid'),
                    'value' => $this->driver->rfid
                ]],
            'table'  => $table,
            'totals'  => $this->getTotals($totals, ['fuel_consumption', 'distance', 'engine_hours', 'fuel_100', 'fuel_h']),
        ];
    }

    /**
     * @param Device $tankDevice
     * @return Group|null
     * @throws \Exception
     */
    private function getTankData(Device $tankDevice)
    {
        $this->actionGroup = 'tank';

        $tankData = $this->getDeviceHistoryData($tankDevice)['groups']->first();

        if ($tankData === null) {
            return null;
        }

        if (!$tankData->stats()->get('fuel_consumption')->value()) {
            return null;
        }

        return $tankData;
    }

    /**
     * @param Device $vehicleDevice
     * @param Group $tankGroup
     * @return Group|null
     * @throws \Exception
     */
    private function getVehicleData(Device $vehicleDevice, Group $tankGroup)
    {
        $this->actionGroup = 'vehicle';

        $vehicleData = $this->getDeviceHistoryData($vehicleDevice)['root'];

        if (!$vehicleData)
            return null;

        $vehicleStats = $vehicleData->stats();

        if (!$vehicleStats->get('duration')->value())
            return null;

        $fuel = (float)$tankGroup->stats()->get('fuel_consumption')->value();

        $this->setFuelAvgDistance($vehicleData, $fuel);
        $this->setFuelAvgEngine($vehicleData, $fuel);

        return $vehicleData;
    }

    private function getConnectionsInfo()
    {
        $this->connectionsInfo = [];

        $query = $this->getConnectionQuery();

        $info = (clone $query)
            ->selectSub(function (Builder $query) {
                $query->select('p_to.date')
                    ->from('user_driver_position_pivot AS p_to')
                    ->whereColumn('p_to.date', '>', 'p.date')
                    ->whereColumn('p_to.driver_id', 'p.driver_id')
                    ->orderBy('p_to.date', 'ASC')
                    ->limit(1);
            }, 'date_to')
            ->whereBetween('date', [$this->date_from, $this->date_to])
            ->orderBy('date')
            ->distinct()
            ->get()
            ->toArray();

        $iMax = count($info) - 1;

        if ($iMax < 0) {
            return;
        }

        if ($info[$iMax]->date_to) {
            $now = \Carbon::now();
            $dateTo = \Carbon::parse($info[$iMax]->date_to);

            if ($now->diffInDays($dateTo) > 3) {
                $info[$iMax]->date_to = $this->date_to;
            }

        } elseif (!$info[$iMax]->date_to) {
            $info[$iMax]->date_to = $this->date_to;
        }

        $this->connectionsInfo = $info;
    }

    private function getPreFirstDateFrom()
    {
        $query = $this->getConnectionQuery();

        $origDateFrom = $this->date_from;
        $origDateTo = $this->date_to;

        $minDate = \Carbon::parse($this->connectionsInfo[0]->date)->subDays(3)->format('Y-m-d H:i:s');
        $preFirstDateTo = $this->connectionsInfo[0]->date;

        do {
            $preFirst = (clone $query)
                ->where('date', '<', $preFirstDateTo)
                ->orderBy('date', 'DESC')
                ->first();

            if ($preFirst) {
                $this->date_from = $preFirst->date;
                $this->date_to = $preFirstDateTo;

                $tankDevice = runCacheEntity(Device::class, $preFirst->device_id)->first();
                $tankData = $this->getTankData($tankDevice);

                $preFirstDateTo = $preFirst->date;
            }
        } while ($preFirst && !$tankData && $preFirstDateTo > $minDate);

        $this->date_from = $origDateFrom;
        $this->date_to = $origDateTo;

        return isset($preFirst->date) && isset($tankData)
            ? $preFirst->date
            : $this->connectionsInfo[0]->date;
    }

    private function getConnectionQuery(): Builder
    {
        return DB::query()
            ->select(['p.date', 'p.device_id'])
            ->from('user_driver_position_pivot AS p')
            ->where('p.driver_id', $this->driver->id);
    }

    private function setFuelAvgDistance(Group $group, $fuel)
    {
        $distance = $group->stats()->has('distance') ? $group->stats()->get('distance')->value() : 0;

        $formatter = clone Formatter::capacity();
        $formatter->setUnit(trans('front.l_km'));

        $stat = (new StatValue())->setFormatUnit($formatter);

        if ($distance) {
            $stat->set($fuel / $distance * 100);
        }

        $group->stats()->set('fuel_100', $stat);
    }

    private function setFuelAvgEngine(Group $group, $fuel)
    {
        $engineHours = 0;

        if ($group->stats()->has('engine_hours')) {
            $group->stats()->get('engine_hours')->getFormatUnit()->setFormat('number');
            $engineHours = $group->stats()->get('engine_hours')->value();
        }

        $formatter = clone Formatter::capacity();
        $formatter->setUnit(trans('front.l_h'));

        $stat = (new StatValue())->setFormatUnit($formatter);

        if ($engineHours) {
            $stat->set($fuel / $engineHours * 3600);
        }

        $group->stats()->set('fuel_h', $stat);
    }

    public static function isEnabled()
    {
        return config('addon.report_fuel_tank_usage');
    }
}