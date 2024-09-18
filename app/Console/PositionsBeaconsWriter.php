<?php

namespace App\Console;

use Illuminate\Support\Facades\Redis;
use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Services\DeviceBeaconsService;

class PositionsBeaconsWriter
{
    private $debug;
    private $deviceBeaconsService;
    private $processManager;

    private $skipBeacons;
    private $detectionSpeed;

    /**
     * @var PositionsWriter[]
     */
    private $writers = [];

    public function __construct(Device $device, bool $debug)
    {
        $this->device = $device;
        $this->debug = $debug;

        $beaconsSettings = settings('plugins.beacons');

        if ($beaconsSettings) {
            $this->skipBeacons = !($beaconsSettings['status'] && !$device->isBeacon());
            $this->detectionSpeed = $beaconsSettings['options']['detection_speed'] ?? null;
        } else {
            $this->skipBeacons = true;
            $this->detectionSpeed = 0;
        }

        if (!$this->skipBeacons) {
            $this->deviceBeaconsService = new DeviceBeaconsService($device);
            $this->processManager = new ProcessManager('insert:run', 60, 10000);
            $this->positionsStack = new PositionsStack();
        }
    }

    /**
     * @param  TraccarPosition[]  $positions
     */
    public function write(array $positions)
    {
        if ($this->skipBeacons || empty($positions)) {
            return;
        }

        foreach ($positions as $position) {
            \DB::transaction(function () use ($position) {
                $this->loadPosition($position);
            });
        }

        $this->flushData();
    }

    private function flushData()
    {
        foreach ($this->writers as $imei => $writer) {
            $writer->runList($imei);
            $this->processManager->unlock($imei);
        }

        $this->writers = [];
    }

    private function loadPosition(TraccarPosition $position)
    {
        if (!$this->isPositionValid($position)) {
            return;
        }

        $positionData = [
            'speed'       => $position->speed,
            'altitude'    => $position->altitude,
            'latitude'    => $position->latitude,
            'longitude'   => $position->longitude,
            'course'      => $position->course,
            'deviceTime'  => strtotime($position->time) * 1000,
            'fixTime'     => strtotime($position->time) * 1000,
            'valid'       => $position->valid,
            'protocol'    => 'beacon'
        ];

        $beaconsData = $this->extractBeaconsFromPosition($position);
        $beacons = [];

        foreach ($beaconsData as $beaconData) {
            $imei = $beaconData['uuid'] ?? null;

            if (is_null($imei)) {
                $imei = $beaconData['namespace'] ?? null;
            }

            if (!$imei) {
                continue;
            }

            $beaconData['imei'] = $imei;

            $beacon = \Cache::store('array')->sear('beacon_' . $imei, function () use ($imei) {
                return Device::kindBeacon()->where('imei', $imei)->first() ?: false;
            });

            if (!$beacon) {
                continue;
            }

            $beacons[] = $beacon;

            if (!isset($this->writers[$imei])) {
                $this->processManager->lock($imei);
                $this->writers[$imei] = new PositionsWriter($beacon, $this->debug);
            }

            $this->positionsStack->add($beaconData + $positionData);
        }

        $this->deviceBeaconsService->setCurrentBeacons($beacons, $position->time);
    }

    private function isPositionValid(TraccarPosition $position): bool
    {
        return $position->speed >= $this->detectionSpeed;
    }

    private function extractBeaconsFromPosition(TraccarPosition $position): array
    {
        $regex = '/^beacon(\d+)(.*)$/';
        $beacons = [];

        foreach ($position->parameters as $key => $value) {
            if (strpos($key, 'beacon') !== 0) {
                continue;
            }

            preg_match($regex, $key, $matches);
            $prefix = $matches[1];
            $param = $matches[2];

            $beacons[$prefix][$param] = $value;
        }

        return $beacons;
    }
}