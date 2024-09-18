<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\GroupLoad;
use Tobuli\History\Actions\LoadCount;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class LoadReport extends DeviceHistoryReport
{
    const TYPE_ID = 37;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.loading_unloading');
    }

    protected function getActionsList()
    {
        LoadCount::$loadStates = [1, 0];
        GroupLoad::$loadStates = [1, 0];

        return [
            GroupLoad::class,
            LoadCount::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        /** @var Group $group */
        foreach ($data['groups']->all() as $group)
        {
            $row = $this->getDataFromGroup($group, [
                'previous_load',
                'current_load',
                'difference',
                'location',
            ]);

            $row['time'] = $group->getEndAt();
            $row['state'] = $group->getEndPosition()->loadChange['state'];

            $rows[] = $row;
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function getDeviceMeta($device)
    {
        $metas = parent::getDeviceMeta($device);

        $metas['sensor'] = [
            'title' => trans('front.sensor'),
            'value' => $device->getLoadSensor()->name ?? '',
        ];

        return $metas;
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return parent::getTotals($group, ['loading_count', 'unloading_count']);
    }

    protected function isEmptyResult($data)
    {
        return empty($data['groups']) || empty($data['groups']->all());
    }
}