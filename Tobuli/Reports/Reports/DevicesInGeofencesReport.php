<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\GroupGeofenceIn;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class DevicesInGeofencesReport extends DeviceHistoryReport
{
    const TYPE_ID = 80;

    protected $disableFields = ['speed_limit', 'stops', 'show_addresses', 'zones_instead'];
    protected $validation = ['geofences' => 'required'];

    protected $skip_blank_results = true;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.devices_in_geofences');
    }

    protected function getActionsList()
    {
        return [GroupGeofenceIn::class];
    }

    protected function extendStart()
    {
        return 60 * 24 * 2;
    }

    protected function getTable($data)
    {
        $rows = [];

        /** @var Group $group */
        foreach ($data['groups']->all() as $group) {
            $rows[$group->geofence_id] = $this->getDataFromGroup($group, 'group_geofence');
        }

        return $rows;
    }

    protected function isEmptyResult($data)
    {
        return empty($data) || empty($data['groups']->all());
    }

    protected function afterGenerate()
    {
        $items = [];

        foreach ($this->items as $item) {
            foreach ($item['table'] as $id => $geofence) {
                if (isset($items[$id])) {
                    $items[$id]['devices'][] = $item['meta'];
                } else {
                    $items[$id] = [
                        'name' => $geofence['group_geofence'],
                        'devices' => [$item['meta']]
                    ];
                }
            }
        }

        $this->items = $items;
    }
}