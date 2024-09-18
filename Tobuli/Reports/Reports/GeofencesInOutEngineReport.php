<?php

namespace Tobuli\Reports\Reports;

use Tobuli\Entities\Geofence;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineOnOff;
use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class GeofencesInOutEngineReport extends DeviceHistoryReport
{
    const TYPE_ID = 20;

    protected $validation = ['geofences' => 'required'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_in_out').' ('.trans('front.ignition_on_off').')';
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            EngineOnOff::class,

            GroupGeofenceIn::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];
        $totals = [];

        $keys = [];

        foreach ($data['groups']->all() as $group)
        {
            $row = $this->getDataFromGroup($group, [
                'start_at',
                'end_at',
                'engine_on_duration',
                'engine_off_duration',
                'location',
                'group_geofence'
            ]);

            $row['status'] = trans('front.on');
            $row['duration'] = $row['engine_on_duration'];
            $rows[] = $row;

            $row['status'] = trans('front.off');
            $row['duration'] = $row['engine_off_duration'];
            $rows[] = $row;

            if ( ! isset($keys[$group->getKey()]))
                $keys[$group->getKey()] = runCacheEntity(Geofence::class, $group->geofence_id)->implode('name', ', ');
        }

        foreach ($keys as $key => $geofence)
        {
            $group = $data['groups']->merge($key);


            $total = $this->getDataFromGroup($group, [
                'engine_on_duration',
            ]);

            $total['geofence_name'] = $geofence;

            $totals[] = $total;
        }

        return [
            'rows'   => $rows,
            'totals' => $totals,
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