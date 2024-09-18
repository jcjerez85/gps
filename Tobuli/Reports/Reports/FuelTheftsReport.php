<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\GroupFuelThefting;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class FuelTheftsReport extends DeviceHistoryReport
{
    const TYPE_ID = 12;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.fuel_thefts');
    }

    protected function getActionsList()
    {
        $list = [
            GroupFuelThefting::class,
        ];

        return $list;
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $rows[] = $this->getDataFromGroup($group, [
                'start_at',
                'location',
                'fuel_level_previous',
                'fuel_level_current',
                'fuel_level_difference',
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return [];
    }

    protected function isEmptyResult($data)
    {
        return empty($data['groups']) || empty($data['groups']->all());
    }
}