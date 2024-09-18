<?php

namespace Tobuli\Services\Cleaner;

use Carbon\Carbon;
use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarDevice;

class DeviceDateCleaner extends AbstractCleaner
{
    protected $dateField = 'time';
    private $i = 0;
    private $all = 0;

    public function clean()
    {
        $this->i = 0;
        $this->all = TraccarDevice::count();

        TraccarDevice::orderBy('id', 'asc')->chunk(500, function ($devices) {
            /** @var Device $device */
            foreach ($devices as $device) {
                $this->i++;

                $date = $this->getDate($device);

                $msg = "CLEAN TABLES ({$this->i}/{$this->all}) Device {$device->id}";

                try {
                    $query = $device->positions()->where(function ($q) use ($date) {
                        $q->whereNull($this->dateField);

                        if ($date) {
                            $q->orWhere($this->dateField, '<', $date);
                        }
                    })->limit($this->limit);

                    do {
                        $deleted = (clone $query)->delete();
                    } while ($deleted > 0);

                    ($this->output)(true, $msg . ' ' . $date);
                } catch (\Exception $e) {
                    ($this->output)(false, $msg . ' ' . $e->getMessage());
                }
            }
        });
    }

    protected function getDate(Device $device)
    {
        return Carbon::parse($this->date);
    }
}