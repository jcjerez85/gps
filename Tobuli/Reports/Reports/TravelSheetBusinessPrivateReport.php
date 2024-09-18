<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GroupDriveBusinessPrivate;
use Tobuli\History\Actions\Odometer;
use Tobuli\History\Actions\OdometerEnd;
use Tobuli\History\Actions\OdometerStart;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class TravelSheetBusinessPrivateReport extends DeviceHistoryReport
{
    const TYPE_ID = 61;

    public function getInputParameters(): array
    {
        return [
            \Field::multiSelect('drive_types', trans('validation.attributes.type'))
                ->setOptions(TravelSheetBusinessPrivateReport::getDriveTypes())
            ,
        ];
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.travel_sheet') . ' ('.trans('front.business').'/'.trans('front.private').')';
    }

    public static function isEnabled()
    {
        return settings('plugins.business_private_drive.status');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            Drivers::class,
            Odometer::class,
            OdometerStart::class,
            OdometerEnd::class,

            GroupDriveBusinessPrivate::class,
        ];
    }

    public static function getDriveTypes()
    {
        return [
            'drive_business' => trans('front.business'),
            'drive_private'  => trans('front.private'),
            'drive'          => trans('front.not_available'),
        ];
    }

    protected function getTable($data)
    {
        $types = $this->parameters['drive_types'] ?? [];
        $total = new Group('total');
        $rows  = [];

        foreach ($data['groups']->all() as $group)
        {
            if ($types && !in_array($group->getKey(), $types))
                continue;

            $row = $this->getDataFromGroup($group, [
                'group_key',
                'start_at',
                'end_at',
                'duration',
                'distance',
                'drivers',
                'speed_max',
                'speed_avg',
                'location_start',
                'location_end',
                'fuel_consumption_list',
                'fuel_price_list',
                'odometer'
            ]);

            $row['drive_type'] = self::getDriveTypes()[$group->getKey()] ?? null;

            $row['odometer_start'] = $group->stats()->has('odometer_start')
                ? $group->stats()->human('odometer_start')
                : trans('front.not_available');

            $row['odometer_end'] = $group->stats()->has('odometer_end')
                ? $group->stats()->human('odometer_end')
                : trans('front.not_available');

            $rows[] = $row;

            $total->applyArray($group->stats()->all());
        }

        return [
            'rows'   => $rows,
            'totals' => $this->getDataFromGroup($total, [
                'duration',
                'distance',
                'fuel_consumption_list',
                'fuel_price_list'
            ]),
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return parent::getTotals($group, ['distance', 'drive_duration']);
    }
}