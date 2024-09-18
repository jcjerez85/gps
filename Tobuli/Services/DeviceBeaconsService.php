<?php

namespace Tobuli\Services;

use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Device;

class DeviceBeaconsService
{
    const BEACON_ACTION_DISCONNECT = 0;
    const BEACON_ACTION_CONNECT = 1;

    private $beaconIds;
    private $device;
    private $historyLogEnabled;
    private $currentLogEnabled;
    private $detachBeaconsOnNoPositionData;

    public function __construct(Device $device)
    {
        $this->setDevice($device);

        $settings = settings('plugins.beacons');
        $this->historyLogEnabled = $settings['options']['log']['history'] ?? false;
        $this->currentLogEnabled = $settings['options']['log']['current'] ?? false;
        $this->detachBeaconsOnNoPositionData = $settings['options']['log']['detach_on_no_position_data'] ?? false;
    }

    /**
     * @param  Device[]|int[]  $beacons
     * @throws \Throwable
     */
    public function setCurrentBeacons(array $beacons, $date = null)
    {
        if (!$this->prepare($beacons, $date)) {
            return;
        }

        $newBeacons = array_diff($beacons, $this->beaconIds);
        $removedBeacons = array_diff($this->beaconIds, $beacons);

        if (count($newBeacons) === 0 && count($removedBeacons) === 0) {
            return;
        }

        $this->removeBeacons($removedBeacons, $date);

        $this->addBeacons($beacons, $date);
    }

    public function removeBeacons(array $beacons, $date = null)
    {
        if (!$this->detachBeaconsOnNoPositionData) {
            return;
        }

        if (!$this->prepare($beacons, $date)) {
            return;
        }

        $this->beaconIds = array_diff($this->beaconIds, $beacons);

        if ($this->currentLogEnabled) {
            DB::table('device_current_beacons_pivot')
                ->where('device_id', $this->device->id)
                ->whereIn('beacon_id', $beacons)
                ->delete();
        }

        if ($this->historyLogEnabled) {
            DB::table('device_history_beacons_pivot')->insert(
                array_map(function ($beacon) use ($date) {
                    return ['device_id' => $this->device->id, 'beacon_id' => $beacon, 'date' => $date, 'action' => self::BEACON_ACTION_DISCONNECT];
                }, $beacons)
            );
        }
    }

    public function addBeacons(array $beacons, $date = null)
    {
        if (!$this->prepare($beacons, $date)) {
            return;
        }

        $this->beaconIds = array_unique(array_merge($this->beaconIds, $beacons));

        if ($this->historyLogEnabled) {
            DB::table('device_history_beacons_pivot')->insert(
                array_map(function ($beacon) use ($date) {
                    return ['device_id' => $this->device->id, 'beacon_id' => $beacon, 'date' => $date, 'action' => self::BEACON_ACTION_CONNECT];
                }, $beacons)
            );

            $disconnect = self::BEACON_ACTION_DISCONNECT;
            $beaconsIn = implode(', ', $beacons);

            DB::statement("
                INSERT INTO device_history_beacons_pivot
                     SELECT device_id, beacon_id, '$date', $disconnect
                       FROM device_current_beacons_pivot
                      WHERE device_id != {$this->device->id} AND beacon_id IN ($beaconsIn)
            ");
        }

        if ($this->currentLogEnabled) {
            DB::table('device_current_beacons_pivot')->insert(
                array_map(function ($beacon) {
                    return ['device_id' => $this->device->id, 'beacon_id' => $beacon];
                }, $beacons)
            );

            DB::table('device_current_beacons_pivot')->whereIn('beacon_id', $beacons)->where('device_id', '!=', $this->device->id)->delete();
        }
    }

    private function prepare(array &$input, &$date): bool
    {
        if (empty($beacons)) {
            return false;
        }

        if ($this->beaconIds === null) {
            $this->beaconIds = $this->device->beacons()->pluck('beacon_id')->all();
        }

        if ($date === null) {
            $date = date('Y-m-d H:i:s');
        }

        array_walk($input, function (&$beacon) {
            if ($beacon instanceof Device) {
                $beacon = $beacon->id;
            }
        });

         return true;
    }

    public function setDevice(Device $device)
    {
        if ($device->isBeacon()) {
            throw new \InvalidArgumentException('Device cannot be beacon');
        }

        $this->device = $device;
        $this->beaconIds = null;
    }
}