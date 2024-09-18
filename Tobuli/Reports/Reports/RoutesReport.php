<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\Odometer;
use Tobuli\History\Actions\Overspeed;
use Tobuli\History\Actions\OverspeedStatic;
use Tobuli\History\Actions\Route;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class RoutesReport extends DeviceHistoryReport
{
    const TYPE_ID = 43;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];
    protected $formats = ['html', 'json'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.routes');
    }

    public static function isEnabled() {
        return ! settings('plugins.routes_report.status');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            EngineHours::class,
            Drivers::class,
            OverspeedStatic::class,
            Odometer::class,
            Route::class,
        ];
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        return [
            'meta'   => $this->getDeviceMeta($device),
            'map'    => $this->getMap($data),
            'totals' => $this->getTotals($data['root'])
        ];
    }
}