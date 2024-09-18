<?php

namespace Tobuli\Lookups\Tables;

use Formatter;
use Illuminate\Support\Arr;
use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupObjectList;

class ObjectListLookupTable extends LookupTable
{
    protected $autorefresh = 15;

    public function getRoutes($options = [])
    {
        return [
            'index'  => route('objects.listview', $options),
            'table'  => route('objects.listview.table', $options),
            'data'   => route('objects.listview.data', $options),
            'edit'   => route('objects.listview.edit', $options),
            'update' => route('objects.listview.update', $options),

            'csv'    => route('objects.listview.data', $options + ['action' => 'csv']),
            'excel'  => route('objects.listview.data', $options + ['action' => 'excel']),
            'pdf'    => route('objects.listview.data', $options + ['action' => 'pdf']),
        ];
    }

    protected function getLookupClass()
    {
        return LookupObjectList::class;
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
    public function getDefaultColumns() {
        return [
            'name',
            'imei',
            'time',
            'position'
        ];
    }

    /*
     * @return array
     */
    public function getRememberColumns() {
        return $this->lookupModel()->getColumns()->pluck('data')->toArray();
    }

    public function baseQuery()
    {
        $query = $this->getUser()->devices();

        //remove default order in relationship
        $query->getQuery()->clearOrdersBy();

        $query->traccarJoin();

        //prevent columns dublication
        $query->addSelect("user_device_pivot.group_id");

        $query->with([
            'sensors',
            'traccar',
        ]);

        switch (Arr::get($this->lookupModel()->getSettings(), 'groupby')) {
            case 'protocol':
                $query->traccarJoin()->orderBy('traccar_devices.protocol');
                break;
            case 'group':
                $query->orderBy('user_device_pivot.group_id');
                break;
        }

        return $query;
    }

    public function getColumns() {
        return $this->getCurrentColumns()->toArray();
    }

    /*
     * @return boolean
     */
    public function checkPermission()
    {
        if ( ! settings('plugins.object_listview.status'))
            return false;

        return $this->getUser()->can('view', $this->lookupModel()->model());
    }

    public function getRowActions($device)
    {
        $user = $this->getUser();

        if ( ! $user)
            return [];

        $actions = [];

        if ($user->can('edit', $device))
            $actions[] = [
                'title' => trans('global.edit'),
                'url'   => route("devices.edit", [$device->id]),
                'modal' => 'devices_edit',
            ];

        if ($user->can('remove', $device))
            $actions[] = [
                'title' => trans('global.delete'),
                'url'   => route("devices.do_destroy", [$device->id]),
                'modal' => 'devices_delete',
            ];

        if ($user->can('view', $device))
            $actions[] = [
                'title' => trans('front.follow'),
                'url'   => route("devices.follow_map", $device->id),
                'onClick' => "dialogWindow(event, '{$device->name}')"
            ];

        if ($user->perm('call_actions', 'edit'))
            $actions[] = [
                'title' => trans('front.call_action'),
                'url'   => route("call_actions.create", ['device_id' => $device->id]),
                'modal' => 'call_action_create',
            ];

        return $actions;
    }

    protected function renderColumn($model, $column) {
        if (Arr::get($column, 'datatype') != 'sensor') {
            if ($this->isHtmlBuild())
                return $this->lookupModel()->renderHtml($model, $column['data']);
            else
                return $this->lookupModel()->render($model, $column['data']);
        }

        if ($this->isHtmlBuild())
            return $this->lookupModel()->renderHtmlSensor($model, $column['data']);
        else
            return $this->lookupModel()->renderSensor($model, $column['data']);
    }
}