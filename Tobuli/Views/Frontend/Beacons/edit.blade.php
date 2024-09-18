<?php /** @var \Tobuli\Entities\Device $item */ ?>
@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon device"></i> {!!trans('global.edit')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#device-form-main" role="tab" data-toggle="tab">{!!trans('front.main')!!}</a></li>
        <li><a href="#device-form-icons" role="tab" data-toggle="tab">{!!trans('front.icons')!!}</a></li>
        <li><a href="#device-form-advanced" role="tab" data-toggle="tab">{!!trans('front.advanced')!!}</a></li>
        <li><a href="#device-form-sensors" role="tab" data-toggle="tab">{!!trans('front.sensors')!!}</a></li>
        <li><a href="#device-form-tail" role="tab" data-toggle="tab">{!!trans('front.tail')!!}</a></li>
        @if(expensesTypesExist())
            <li><a href="#device-form-expenses" role="tab" data-toggle="tab" data-url="{{ route('device_expenses.index', $item->id) }}">{!!trans('front.expenses')!!}</a></li>
        @endif
    </ul>

    {!!Form::open(['route' => ['beacons.update', $item->id], 'method' => 'PUT'])!!}
    {!!Form::hidden('id', $item->id)!!}
    <?php
    $additional_fields_on = settings('plugins.additional_installation_fields.status');
    ?>
    <div class="tab-content">
        <div id="device-form-main" class="tab-pane active">
            @if (isAdmin())
                <div class="form-group">
                    <div class="checkbox-inline">
                        {!! Form::hidden('active', 0) !!}
                        {!! Form::checkbox('active', 1, $item->active) !!}
                        {!! Form::label(null, trans('validation.attributes.active')) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('user_id', trans('validation.attributes.user').'*:') !!}
                    {!! Form::select('user_id[]', $users->pluck('email', 'id'), $sel_users, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => 'true']) !!}
                </div>
            @endif

            <div class="form-group">
                {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
                {!!Form::text('name', $item->name, ['class' => 'form-control'])!!}
            </div>


            @if(Auth::user()->can('view', $item, 'imei'))
                <div class="form-group">
                    <label for="imei">{{ trans('front.tracker_id') }}:</label>
                    {!!Form::text('imei', $item->imei, ['class' => 'form-control', 'placeholder' => trans('front.imei_placeholder')] + ( ! Auth::user()->can('edit', $item, 'imei') ? ['disabled' => 'disabled'] : []) )!!}
                </div>
            @endif
        </div>
        <div id="device-form-icons" class="tab-pane">
            <div class="form-group">
                {!!Form::label('device_icons_type', trans('Seleccione tipo de icono').':')!!}
                {!!Form::select('device_icons_type', $icons_type, $item->icon->type, ['class' => 'form-control'])!!}
            </div>

            {!!Form::hidden('icon_id')!!}
            @foreach($device_icons_grouped as $group => $icons)
                <div class="device-icons-{{ $group }} device-icons-group">
                    <div class="form-group">
                        {!!Form::label('icon_idd', trans('validation.attributes.icon_id').':')!!}
                    </div>
                    <div class="icon-list">
                        @foreach($icons as $icon)
                            <div class="checkbox-inline">
                                {!! Form::radio('icon_id', $icon->id, ($item['icon_id'] == $icon['id'])) !!}
                                <label>
                                    <img src="{!!asset($icon->path)!!}" alt="ICON"
                                         style="width: {!!$icon->width!!}px; height: {!!$icon->height!!}px;"/>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="device-icons-arrow">
                <div class="form-group">
                    {!!Form::label('icon_moving', trans('front.moving').':')!!}
                    {!!Form::select('icon_moving', $device_icon_colors, $item->icon_colors['moving'], ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_stopped', trans('front.stopped').':')!!}
                    {!!Form::select('icon_stopped', $device_icon_colors, $item->icon_colors['stopped'], ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_offline', trans('front.offline').':')!!}
                    {!!Form::select('icon_offline', $device_icon_colors, $item->icon_colors['offline'], ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_engine', trans('front.engine_idle').':')!!}
                    {!!Form::select('icon_engine', $device_icon_colors, $item->icon_colors['engine'], ['class' => 'form-control'])!!}
                </div>
            </div>
        </div>
        <div id="device-form-advanced" class="tab-pane">
            <div class="form-group">
                {!!Form::label('group_id', trans('validation.attributes.group_id').':')!!}
                {!!Form::select('group_id', $device_groups, $group_id, ['class' => 'form-control', 'data-live-search' => 'true'])!!}
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!!Form::label('vin', trans('validation.attributes.vin').':')!!}
                        {!!Form::text('vin', $item->vin, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('device_model', trans('validation.attributes.device_model').':')!!}
                        {!!Form::text('device_model', $item->device_model, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('object_owner', trans('validation.attributes.object_owner').':')!!}
                        {!!Form::text('object_owner', $item->object_owner, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!!Form::label('plate_number', trans('validation.attributes.plate_number').':')!!}
                        {!!Form::text('plate_number', $item->plate_number, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('registration_number', trans('validation.attributes.registration_number').':')!!}
                        {!!Form::text('registration_number', $item->registration_number, ['class' => 'form-control'])!!}
                    </div>

                </div>
            </div>

            <div class="form-group">
                {!!Form::label('additional_notes', trans('validation.attributes.additional_notes').':')!!}
                {!!Form::text('additional_notes', $item->additional_notes, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('comment', trans('validation.attributes.comment').':')!!}
                {!!Form::text('comment', $item->comment, ['class' => 'form-control'])!!}
            </div>
        </div>
        <div id="device-form-sensors" class="tab-pane">
            <div class="action-block">
                <a href="javascript:" class="btn btn-action" data-url="{!!route('sensors.create', $item->id)!!}"
                   data-modal="sensors_create" type="button">
                    <i class="icon add"></i> {{ trans('front.add_sensor') }}
                </a>
            </div>
            <div data-table>
                @include('front::Sensors.index')
            </div>
            @if (isAdmin())
                <div class="form-group">
                    {!! Form::label('sensor_group_id', trans('validation.attributes.sensor_group_id').':') !!}
                    {!! Form::select('sensor_group_id', $sensor_groups, null, ['class' => 'form-control']) !!}
                </div>
            @endif
        </div>
        <div id="device-form-tail" class="tab-pane">
            <div class="form-group">
                {!!Form::label('tail_color', trans('validation.attributes.tail_color').':')!!}
                {!!Form::text('tail_color', $item->tail_color, ['class' => 'form-control colorpicker'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('tail_length', trans('validation.attributes.tail_length').' (0-10 '.trans('front.last_points').'):')!!}
                {!!Form::text('tail_length', $item->tail_length, ['class' => 'form-control'])!!}
            </div>
        </div>
        @if(expensesTypesExist())
            <div id="device-form-expenses" class="tab-pane"></div>
        @endif
    </div>
    {!!Form::close()!!}
    <script>
        $(document).ready(function () {
            $('select[name="device_icons_type"]').trigger('change');
        });

        tables.set_config('device-form-sensors', {
            url: '{!!route('sensors.index', $item->id)!!}'
        });

        function sensors_create_modal_callback() {
            tables.get('device-form-sensors');
        }

        function sensors_edit_modal_callback() {
            tables.get('device-form-sensors');
        }

        function sensors_destroy_modal_callback() {
            tables.get('device-form-sensors');
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
    @if (Auth::User()->perm('devices', 'remove'))
        <a href="javascript:" data-modal="objects_delete" class="btn btn-danger"
           data-url="{{ route("devices.do_destroy", ['id' => $item->id]) }}">
            {{ trans('global.delete') }}
        </a>
    @endif
@stop