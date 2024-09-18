<?php

namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Database\QueryException;
use Tobuli\Reports\DeviceReport;

class EngineHoursCurrentReport extends DeviceReport
{
    private $lastHoursValue;
    private $lastVirtualHoursValue;

    const TYPE_ID = 58;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.engine_hours') . ' ' . trans('front.current');
    }

    public static function isEnabled()
    {
        return config('addon.engine_hours_current_report');
    }

    protected function processPosition($position)
    {
        if ($value = round($position->getEngineHours() / 3600, 2))
            $this->lastHoursValue = $value;

        if ($value = round($position->getVirtualEngineHours() / 3600, 2))
            $this->lastVirtualHoursValue = $value;
    }

    protected function generateDevice($device)
    {
        $this->lastHoursValue = null;
        $this->lastVirtualHoursValue = null;

        try {
            $device->positions()
                ->orderliness()
                ->whereBetween('time', [$this->date_from, $this->date_to])
                ->union(
                    $device->positions()
                        ->where('time', '<', $this->date_from)
                        ->orderliness()
                        ->limit(1)
                )
                ->chunk(2000, function ($positions) {
                    foreach ($positions as $position)
                        $this->processPosition($position);
                });
        } catch (QueryException $e) {}

        if (empty($this->lastHoursValue) && empty($this->lastVirtualHoursValue))
            return null;

        return [
            'meta' => $this->getDeviceMeta($device),
            'totals' => [
                'hours' => is_null($this->lastHoursValue) ? 'N/A' : $this->lastHoursValue . trans('front.hour_short'),
                'virtual_hours' => is_null($this->lastHoursValue) ? $this->lastVirtualHoursValue . trans('front.hour_short') : 'N/A',
            ]
        ];
    }
}
