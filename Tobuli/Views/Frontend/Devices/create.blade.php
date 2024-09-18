<?php $item = new \Tobuli\Entities\Device(); ?>
@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon device"></i> {!!trans('global.add_new')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#device-add-form-main" role="tab" data-toggle="tab">{!!trans('front.main')!!}</a></li>
        <li><a href="#device-add-form-icons" role="tab" data-toggle="tab">{!!trans('front.icons')!!}</a></li>
        <li><a href="#device-add-form-advanced" role="tab" data-toggle="tab">{!!trans('front.advanced')!!}</a></li>
        @if (isAdmin())
            <li><a href="#device-add-form-sensors" role="tab" data-toggle="tab">{{ trans('front.sensors') }}</a></li>
        @endif
        <li><a href="#device-add-form-accuracy" role="tab" data-toggle="tab">{!!trans('front.accuracy')!!}</a></li>
        <li><a href="#device-add-form-tail" role="tab" data-toggle="tab">{!!trans('front.tail')!!}</a></li>
        <li><a href="javascript:" role="tab" class="disabled">{!!trans('front.services')!!}</a></li>
        @if (Auth::user()->can('view', $item, 'custom_fields') && $item->hasCustomFields())
            <li><a href="#device-custom-fields" role="tab" data-toggle="tab">{!!trans('admin.custom_fields')!!}</a></li>
        @endif
    </ul>

    {!!Form::open(['route' => 'devices.store', 'method' => 'POST'])!!}
    {!!Form::hidden('id')!!}
    <div class="tab-content">
        <div id="device-add-form-main" class="tab-pane active">
            @if (isAdmin())
                <div class="form-group">
                    <div class="checkbox-inline">
                        {!! Form::hidden('active', 0) !!}
                        {!! Form::checkbox('active', 1, true) !!}
                        {!! Form::label(null, trans('validation.attributes.active')) !!}
                    </div>
                </div>
            @endif
            @if (isAdmin() && Auth::User()->can('view', new \Tobuli\Entities\User()))
                <div class="form-group">
                    {!! Form::label('user_id', trans('validation.attributes.user').'*:') !!}
                    {!! Form::select('user_id[]', $users->pluck('email', 'id'), Auth::User()->id, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => 'true']) !!}
                </div>
            @endif

            <div class="form-group">
                {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
                {!!Form::text('name', null, ['class' => 'form-control'])!!}
            </div>

            @if(Auth::user()->can('edit', $item, 'imei'))
                <div class="form-group">
                    <label for="imei">
                        {{ trans('front.device_imei') }} {!! tooltipMarkImei(asset('assets/images/tracker-imei.jpg'), trans('front.tracker_imei_info')) !!}
                        /
                        {{ trans('front.tracker_id') }} {!! tooltipMarkImei(asset('assets/images/tracker-id.jpg'), trans('front.tracker_id_info')) !!}
                        :
                    </label>
                    {!!Form::text('imei', null, ['class' => 'form-control', 'placeholder' => trans('front.imei_placeholder')] )!!}
                </div>
            @endif

            @if (isAdmin() && Auth::user()->can('view', $item, 'expiration_date'))
                <div class="form-group">
                    {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}
                    <div class="input-group">
                        <div class="checkbox input-group-btn">
                            {!! Form::hidden('enable_expiration_date', 0) !!}
                            {!! Form::checkbox('enable_expiration_date', 1, false, Auth::user()->can('edit', $item, 'expiration_date') ? [] : ['disabled' => 'disabled']) !!}
                            {!! Form::label(null) !!}
                        </div>
                        {!! Form::text('expiration_date', NULL, ['class' => 'form-control datetimepicker', 'disabled' => 'disabled']) !!}
                    </div>
                </div>
            @endif

            @if(Auth::User()->able('configure_device'))
            <div class="form-group">
                <div class="checkbox-inline">
                    {!! Form::checkbox('configure_device', 1, false, ['data-disabler' => '#device-form-configurator;hide-disable']) !!}
                    {!! Form::label(null, trans('front.device_configuration')) !!}
                </div>
            </div>
            <div class="form-group" id="device-form-configurator">
                @include('Frontend.DeviceConfig.form', ['showDeviceSelect' => false])
            </div>
            @endif
        </div>
        <div id="device-add-form-icons" class="tab-pane">
            <div class="form-group">
                {!!Form::label('device_icons_type', trans('Seleccione tipo de icono').':')!!}
                {!!Form::select('device_icons_type', $icons_type, null, ['class' => 'form-control'])!!}
            </div>

            {!!Form::hidden('icon_id')!!}
            <?php
            $i = 1;
            $additional_fields_on = settings('plugins.additional_installation_fields.status');
            ?>
            @foreach($device_icons_grouped as $group => $icons)
                <div class="device-icons-{{ $group }} device-icons-group">
                    <div class="form-group">
                        {!!Form::label('icon_idd', trans('validation.attributes.icon_id').':')!!}
                    </div>

                    <div class="icon-list">
                        @foreach($icons as $icon)
                            <div class="checkbox-inline">
                                {!! Form::radio('icon_id', $icon->id, ($i == 1)) !!}
                                <label>
                                    <img src="{!!asset($icon->path)!!}" alt="ICON" style="width: {!!$icon->width!!}px; height: {!!$icon->height!!}px;" />
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="device-icons-arrow">
                <div class="form-group">
                    {!!Form::label('icon_moving', trans('front.moving').':')!!}
                    {!!Form::select('icon_moving', $device_icon_colors, 'green', ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_stopped', trans('front.stopped').':')!!}
                    {!!Form::select('icon_stopped', $device_icon_colors, 'yellow', ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_offline', trans('front.offline').':')!!}
                    {!!Form::select('icon_offline', $device_icon_colors, 'red', ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('icon_engine', trans('front.engine_idle').':')!!}
                    {!!Form::select('icon_engine', $device_icon_colors, 'orange', ['class' => 'form-control'])!!}
                </div>
            </div>
        </div>
        <div id="device-add-form-advanced" class="tab-pane">            
            <div class="form-group">
                {!!Form::label('group_id', trans('validation.attributes.group_id').':')!!}
                {!!Form::select('group_id', $device_groups, null, ['class' => 'form-control', 'data-live-search' => 'true'])!!}
            </div>

            @if(Auth::user()->can('edit', $item, 'device_type_id'))
            <div class="form-group">
                {!!Form::label('device_type_id', trans('validation.attributes.device_type_id').':')!!}
                {!!Form::select('device_type_id', $device_types, null, ['class' => 'form-control'])!!}
            </div>
            @endif

            @if(Auth::user()->can('view', $item, 'authentication'))
                <div class="form-group">
                    {!! Form::label('authentication', trans('validation.attributes.authentication').':') !!}
                    {!! Form::text('authentication',
                        null,
                        ['class' => 'form-control'] + (Auth::user()->can('edit', $item, 'authentication') ? [] : ['disabled']))
                    !!}
                </div>
            @endif

            <div class="row">
                <div class="col-sm-6">                    
                    @if(Auth::user()->can('edit', $item, 'sim_number'))
                        <div class="form-group">
                            {!!Form::label('sim_number', trans('validation.attributes.sim_number').':')!!}
                            {!!Form::text('sim_number', null, ['class' => 'form-control'])!!}
                        </div>
                    @endif

                    @if(Auth::user()->can('view', $item, 'msisdn'))
                        <div class="form-group">
                            {!! Form::label('msisdn', trans('validation.attributes.msisdn').':') !!}
                            {!! Form::text('msisdn',
                                null,
                                ['class' => 'form-control'] + (Auth::user()->can('edit', $item, 'msisdn') ? [] : ['disabled']))
                            !!}
                        </div>
                    @endif

                    @if($additional_fields_on && Auth::user()->can('edit', $item, 'sim_activation_date'))
                        <div class="form-group">
                            {!!Form::label('sim_activation_date', trans('validation.attributes.sim_activation_date').':')!!}
                            {!!Form::text('sim_activation_date', null, ['class' => 'form-control datepicker'])!!}
                        </div>
                    @endif
                    @if($additional_fields_on && Auth::user()->can('edit', $item, 'sim_expiration_date'))
                        <div class="form-group">
                            {!!Form::label('sim_expiration_date', trans('validation.attributes.sim_expiration_date').':')!!}
                            {!!Form::text('sim_expiration_date', null, ['class' => 'form-control datepicker'])!!}
                        </div>
                    @endif
                    <div class="form-group">
                        {!!Form::label('vin', trans('validation.attributes.vin').':')!!}
                        {!!Form::text('vin', null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('device_model', trans('validation.attributes.device_model').':')!!}
                        {!!Form::text('device_model', null, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-sm-6">                    
                    @if($additional_fields_on && Auth::user()->can('edit', $item, 'installation_date'))
                        <div class="form-group">
                            {!!Form::label('installation_date', trans('validation.attributes.installation_date').':')!!}
                            {!!Form::text('installation_date', null, ['class' => 'form-control datepicker'])!!}
                        </div>
                    @endif
                    <div class="form-group">
                        {!!Form::label('plate_number', trans('validation.attributes.plate_number').':')!!}
                        {!!Form::text('plate_number', null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('registration_number', trans('validation.attributes.registration_number').':')!!}
                        {!!Form::text('registration_number', null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">                        
                        {!!Form::label('object_owner', trans('validation.attributes.object_owner').':')!!}
                        {!!Form::text('object_owner', null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('file', 'Subir Archivo:') !!}
                        {!! Form::file('file', ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!!Form::label('additional_notes', trans('validation.attributes.additional_notes').':')!!}
                {!!Form::text('additional_notes', null, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('comment', trans('validation.attributes.comment').':')!!}
                {!!Form::text('comment', null, ['class' => 'form-control'])!!}
            </div>
            @if (config('addon.device_tracker_app_login'))
                <div class="form-group">
                    <div class="checkbox-inline">
                        {!! Form::hidden('app_tracker_login', 0) !!}
                        {!! Form::checkbox('app_tracker_login', 1, 0) !!}
                        {!! Form::label(null, trans('validation.attributes.app_tracker_login')) !!}
                    </div>
                </div>
            @endif
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('gprs_templates_only', 0) !!}
                    {!! Form::checkbox('gprs_templates_only', 1, 0) !!}
                    {!! Form::label('gprs_templates_only', trans('validation.attributes.gprs_templates_only')) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!!Form::label('fuel_measurement_id', trans('validation.attributes.fuel_measurement_type').':')!!}
                        {!!Form::select('fuel_measurement_id', $device_fuel_measurements_select, null, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fuel_quantity">
                            <span class="fuel_title"></span> {!!trans('front.per')!!} <span class="distance_title"></span>:
                        </label>
                        {!!Form::text('fuel_quantity', null, ['class' => 'form-control', 'placeholder' => '0.00', 'id' => 'fuel_quantity'])!!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fuel_price">
                            {!!trans('front.cost_for')!!} <span class="cost_title"></span>:
                        </label>
                        {!!Form::text('fuel_price', null, ['class' => 'form-control', 'placeholder' => '0.00', 'id' => 'fuel_price'])!!}
                    </div>
                </div>
            </div>
            @if(Auth::user()->can('edit', $item, 'forward'))
                <div class="form-group">
                    {!! Form::label(null, trans('validation.attributes.forward').':') !!}
                    <div class="input-group">
                        <div class="checkbox input-group-btn">
                            {!! Form::checkbox('forward[active]', 1, false) !!}
                            {!! Form::label(null) !!}
                        </div>
                        {!! Form::text('forward[ip]', null, ['class' => 'form-control', 'placeholder' => '10.0.0.0:6000']) !!}
                        <div class="input-group-addon">
                            <div class="checkbox-inline">
                                {!! Form::radio('forward[protocol]', 'TCP', true) !!}
                                {!! Form::label(null, 'TCP') !!}
                            </div>
                            <div class="checkbox-inline">
                                {!! Form::radio('forward[protocol]', 'UDP', false) !!}
                                {!! Form::label(null, 'UDP') !!}
                            </div>
                        </div>
                    </div>
                    <small>{!!trans('front.forward_semicolon')!!}</small>
                </div>
            @endif
            <div class="form-group">
                {!!Form::label('timezone_id', trans('validation.attributes.time_adjustment').':')!!}
                {!!Form::select('timezone_id', $timezones, 0, ['class' => 'form-control'])!!}
                <small>{!!trans('front.by_default_time')!!}</small>
            </div>
        </div>
        <div id="device-add-form-sensors" class="tab-pane">
            <div class="form-group">
                {!! Form::label('sensor_group_id', trans('validation.attributes.sensor_group_id').':') !!}
                {!! Form::select('sensor_group_id', $sensor_groups, null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div id="device-add-form-accuracy" class="tab-pane">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('valid_by_avg_speed', 0) !!}
                    {!! Form::checkbox('valid_by_avg_speed', 1, true) !!}
                    {!! Form::label('valid_by_avg_speed', trans('front.valid_by_avg_speed')) !!}
                </div>
            </div>
            <div class="form-group">
                {!!Form::label('min_moving_speed', trans('validation.attributes.min_moving_speed').' ('.trans('front.affects_stops_track',['default'=>6]).'):')!!}
                {!!Form::text('min_moving_speed', '6', ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_fuel_fillings', trans('validation.attributes.min_fuel_fillings').' ('.trans('front.default_value',['default'=>10]).'):')!!}
                {!!Form::text('min_fuel_fillings', '10', ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_fuel_thefts', trans('validation.attributes.min_fuel_thefts').' ('.trans('front.default_value',['default'=>10]).'):')!!}
                {!!Form::text('min_fuel_thefts', '10', ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!! Form::label('fuel_detect_sec_after_stop', trans('validation.attributes.fuel_detect_sec_after_stop').':') !!}
                <div class="input-group">
                    <div class="checkbox input-group-btn">
                        {!! Form::hidden('fuel_detect_sec_after_stop') !!}
                        {!! Form::checkbox('enable_fuel_detect_sec_after_stop', 1, false, ['data-disabler' => 'select[name="fuel_detect_sec_after_stop"];disable']) !!}) !!}
                        {!! Form::label(null) !!}
                    </div>
                    {!! Form::select('fuel_detect_sec_after_stop', $fuel_detect_sec_after_stop_options, $item->fuel_detect_sec_after_stop, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div id="device-add-form-tail" class="tab-pane">
            <div class="form-group">
                {!!Form::label('tail_color', trans('validation.attributes.tail_color').':')!!}
                {!!Form::text('tail_color', '#33CC33', ['class' => 'form-control colorpicker'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('tail_length', trans('validation.attributes.tail_length').' (0-10 '.trans('front.last_points').'):')!!}
                {!!Form::text('tail_length', '5', ['class' => 'form-control'])!!}
            </div>
        </div>
        @if (Auth::user()->can('view', $item, 'custom_fields') && $item->hasCustomFields())
            <div id="device-custom-fields" class="tab-pane">
                @include('Frontend.CustomFields.panel')
            </div>
        @endif
    </div>
    {!!Form::close()!!}

    <script>
        $(document).ready(function() {
            var measurements = {!!json_encode($device_fuel_measurements)!!};

            $(document).on('change', '#devices_create select[name="fuel_measurement_id"]', function () {
                var val = $(this).val();

                $.each(measurements, function (index, value) {
                    if (value.id == val) {
                        $('.distance_title').html(value.distance_title);
                        $('.fuel_title').html(value.fuel_title);
                        $('.cost_title').html(value.cost_title);

                    }
                });
            });

            $(document).on('change', '#devices_create input[name="enable_expiration_date"]', function () {
                if ($(this).prop('checked'))
                    $('input[name="expiration_date"]').removeAttr('disabled');
                else
                    $('input[name="expiration_date"]').attr('disabled', 'disabled');
            });

            $(document).on('change', '#devices_create input[name="forward[active]"]', function () {
                if ($(this).prop('checked'))
                    $('input[name^="forward["]:not([name="forward[active]"])').removeAttr('disabled');
                else
                    $('input[name^="forward["]:not([name="forward[active]"])').attr('disabled', 'disabled');
            });

            $('select[name="device_icons_type"]').trigger('change');

            $('#devices_create input[name="forward[active]"]').trigger('change');

            $('#devices_create select[name="fuel_measurement_id"]').trigger('change');

            $('#devices_create input[name="enable_expiration_date"]').trigger('change');
        });
    </script>
@stop