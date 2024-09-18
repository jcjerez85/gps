<?php

namespace Tobuli\Lookups\Models;

use Formatter;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceGroup;
use Tobuli\Entities\User;
use Tobuli\Lookups\LookupModel;

class LookupDevice extends LookupModel
{
    protected function modelClass()
    {
        return Device::class;
    }

    protected function listColumns() {
        $this->addColumn('id');
        $this->addColumn('active');
        $this->addColumn('name');

        if ($this->user->can('view', $this->model(), 'imei'))
            $this->addColumn('imei');

        if ($this->user->can('view', $this->model(), 'sim_number'))
            $this->addColumn('sim_number');

        $this->addColumn('vin');
        $this->addColumn('device_model');
        $this->addColumn('plate_number');
        $this->addColumn('registration_number');
        $this->addColumn('object_owner');
        $this->addColumn('additional_notes');

        if ($this->user->can('view', $this->model(), 'expiration_date'))
            $this->addColumn('expiration_date');


        if ($this->user->can('view', new User()))
            $this->addColumn([
                'data'       => 'users',
                'name'       => 'users.email',
                'title'      => trans('admin.users'),
                'orderable'  => false,
                'className'  => 'user-list'
            ]);

        if ($this->user->can('view', $this->model(), 'protocol'))
            $this->addColumn([
                'data'       => 'protocol',
                'name'       => 'traccar.protocol',
                'title'      => trans('front.protocol'),

                'orderable'  => true,
                'searchable' => false,
            ]);

        $this->addColumn([
            'data'       => 'speed',
            'name'       => 'traccar.speed',
            'title'      => trans('front.speed'),
            'orderable'  => true,
            'searchable' => false
        ]);

        $this->addColumn([
            'data'       => 'latitude',
            'name'       => 'traccar.lastValidLatitude',
            'title'      => trans('front.latitude'),
            'orderable'  => true,
            'searchable' => false
        ]);

        $this->addColumn([
            'data'       => 'longitude',
            'name'       => 'traccar.lastValidLongitude',
            'title'      => trans('front.longitude'),
            'orderable'  => true,
            'searchable' => false
        ]);

        $this->addColumn([
            'data'       => 'position',
            'name'       => 'position',
            'title'      => trans('front.position'),
            'orderable'  => false,
            'searchable' => false
        ]);


        $this->addColumn([
            'data'       => 'altitude',
            'name'       => 'traccar.altitude',
            'title'      => trans('front.altitude'),
            'orderable'  => true,
            'searchable' => false
        ]);

        $this->addColumn([
            'data'       => 'course',
            'name'       => 'traccar.course',
            'title'      => trans('front.course'),
            'orderable'  => true,
            'searchable' => false
        ]);


        $this->addColumn([
            'data'       => 'ignition',
            'name'       => 'ignition',
            'title'      => trans('front.ignition'),
            'orderable'  => false,
            'searchable' => false,
            'relations'  => ['sensors']
        ]);

        $this->addColumn([
            'data'       => 'status',
            'name'       => 'status',
            'title'      => trans('front.status'),
            'orderable'  => false,
            'searchable' => false,
            'exportable' => false,
            'printable'  => false,
            'relations'  => ['sensors']
        ]);

        $this->addColumn([
            'data'       => 'stop_duration',
            'name'       => 'stop_duration',
            'title'      => trans('front.stop_duration'),
            'orderable'  => false,
            'searchable' => false,
            'relations'  => 'sensors'
        ]);

        $this->addColumn([
            'data'       => 'idle_duration',
            'name'       => 'idle_duration',
            'title'      => trans('front.idle_duration'),
            'orderable'  => false,
            'searchable' => false,
            'relations'  => 'sensors'
        ]);

        $this->addColumn([
            'data'       => 'time',
            'name'       => 'traccar.time',
            'title'      => trans('admin.last_connection'),
            'orderable'  => true,
            'searchable' => false,
        ]);

        $this->addColumn([
            'data'       => 'address',
            'name'       => 'address',
            'title'      => trans('front.address'),
            'orderable'  => false,
            'searchable' => false
        ]);

        if (settings('plugins.additional_installation_fields.status')) {
            if ($this->user->can('view', $this->model(), 'sim_activation_date'))
                $this->addColumn('sim_activation_date');

            if ($this->user->can('view', $this->model(), 'sim_expiration_date'))
                $this->addColumn('sim_expiration_date');

            if ($this->user->can('view', $this->model(), 'installation_date'))
                $this->addColumn('installation_date');
        }

        if ($this->user->can('view', $this->model(), 'authentication'))
            $this->addColumn('authentication');
    }

    public function renderHtmlActive($device) {
        $title = trans('validation.attributes.active');
        $class = $device->active ? 'success' : 'danger';

        return "<span class='label label-sm label-{$class}'>{$title}</span>";
    }

    public function renderHtmlStatus($device) {
        $title = $this->renderStatus($device);
        $color = $device->status_color;

        return "<span class='device-status' style='background-color: {$color};' title='{$title}'></span>";
    }

    public function renderHtmlPosition($device) {
        if ( ! $device->lat)
            return null;

        if ( ! $device->lng)
            return null;

        return googleMapLink($device->lat, $device->lng);
    }

    public function renderActive($device) {
        return $device->active ? trans('global.yes') : trans('global.no');
    }

    public function renderStatus($device) {
        return trans("global.{$device->status}");
    }

    public function renderSpeed($device) {
        return  Formatter::speed()->human($device->getSpeed());
    }

    public function renderExpirationDate($device) {
        if (!$device->hasExpireDate())
            return null;
        
        return  Formatter::time()->human($device->expiration_date);
    }

    public function renderAddress($device) {
        if ( ! $device->lat && ! $device->lng)
            return null;

        return getGeoAddress($device->lat, $device->lng);
    }

    public function renderPosition($device) {
        if ( ! $device->lat && ! $device->lng)
            return null;

        return "{$device->lat}&deg;, {$device->lng}&deg;";
    }

    public function renderIgnition($device) {
        $status = $device->getEngineStatus(true);

        return $status;
    }

    public function renderLastEventType($device) {
        return $device->last_event ? $device->last_event->type_title : null;
    }

    public function renderLastEventTime($device) {
        return $device->last_event ? Formatter::time()->human($device->last_event->time) : null;
    }

    public function renderLastEventTitle($device) {
        return $device->last_event ? $device->last_event->title : null;
    }

    public function renderGroup($device)
    {
        if (empty($device->group_id))
            return null;

        $group = runCacheEntity(DeviceGroup::class, $device->group_id)->first();

        return $group ? $group->title : null;
    }

    public function renderUsers($device)
    {
        return $device->users->filter(function($user){
            return $this->user->can('show', $user);
        })->implode('email', ', ');
    }
}