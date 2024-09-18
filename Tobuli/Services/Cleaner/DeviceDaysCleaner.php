<?php

namespace Tobuli\Services\Cleaner;

use Carbon\Carbon;
use Tobuli\Entities\Device;

class DeviceDaysCleaner extends DeviceDateCleaner
{
    protected function getDate(Device $device)
    {
        if (!$lastConnection = $device->lastConnection) {
            return null;
        }

        $date = Carbon::parse($lastConnection);

        if ($date->gt(Carbon::now())) {
            $date = Carbon::now();
        }

        return $date->subDays($this->date);
    }
}