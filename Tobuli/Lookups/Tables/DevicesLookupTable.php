<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;

class DevicesLookupTable extends LookupTable
{
    protected function getLookupClass()
    {
        return LookupDevice::class;
    }

    /*
     * @return string
     */
    public function getTitle()
    {
        return trans('admin.objects');
    }

    /*
     * @return string
     */
    public function getIcon()
    {
        return 'icon device';
    }

    /*
     * @return array
     */
    public function getDefaultColumns()
    {
        return [
            'name',
            'imei',
            'status',
            'time',
            'expiration_date',
            'protocol',
            'status',
            'users'
        ];
    }

    public function baseQuery()
    {
        $query = $this->getUser()->devices()->with('traccar');

        //remove default order in relationship
        $query->getQuery()->clearOrdersBy();

        //prevent columns dublication
        //$query->select("devices.*");

        $query->traccarJoin();

        return $query;
    }

    public function getRowActions($device)
    {
        $user = $this->getUser();

        if (!$user) {
            return [];
        }

        $actions = [];

        if ($user->can('edit', $device)) {
            $actions[] = [
                'title' => trans('global.edit'),
                'url' => route("devices.edit", [$device->id]),
                'modal' => 'devices_edit',
            ];
        }

        if ($user->can('remove', $device)) {
            $actions[] = [
                'title' => trans('global.delete'),
                'url' => route("devices.do_destroy", [$device->id]),
                'modal' => 'devices_delete',
            ];
        }

        if ($user->can('view', $device)) {
            $actions[] = [
                'title' => trans('front.follow'),
                'url' => route("devices.follow_map", $device->id),
                'onClick' => "dialogWindow(event, '{$device->name}')"
            ];
        }

        if ($user->perm('call_actions', 'edit')) {
            $actions[] = [
                'title' => trans('front.call_action'),
                'url' => route("call_actions.create", ['device_id' => $device->id]),
                'modal' => 'call_action_create',
            ];
        }

        return $actions;
    }
}