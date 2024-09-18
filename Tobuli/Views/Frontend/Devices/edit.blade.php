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
        <li><a href="#device-form-services" role="tab" data-toggle="tab">{!!trans('front.services')!!}</a></li>
        <li><a href="#device-form-accuracy" role="tab" data-toggle="tab">{!!trans('front.accuracy')!!}</a></li>
        <li><a href="#device-form-tail" role="tab" data-toggle="tab">{!!trans('front.tail')!!}</a></li>
        @if(expensesTypesExist())
            <li><a href="#device-form-expenses" role="tab" data-toggle="tab" data-url="{{ route('device_expenses.index', $item->id) }}">{!!trans('front.expenses')!!}</a></li>
        @endif
        @if(Auth::User()->perm('device_camera', 'view'))
            <li><a href="#device-form-cameras" role="tab" data-toggle="tab">{!!trans('front.cameras')!!}</a></li>
        @endif
        @if (Auth::user()->can('view', $item, 'custom_fields') && $item->hasCustomFields())
            <li><a href="#device-custom-fields" role="tab" data-toggle="tab">{!!trans('admin.custom_fields')!!}</a></li>
        @endif
    </ul>

    {!!Form::open(['route' => 'devices.update', 'method' => 'PUT'])!!}
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
            @endif
            @if (isAdmin() && Auth::User()->can('view', new \Tobuli\Entities\User()))
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
                    <label for="imei">
                        {{ trans('front.device_imei') }} {!! tooltipMarkImei(asset('assets/images/tracker-imei.jpg'), trans('front.tracker_imei_info')) !!}
                        /
                        {{ trans('front.tracker_id') }} {!! tooltipMarkImei(asset('assets/images/tracker-id.jpg'), trans('front.tracker_id_info')) !!}
                        :
                    </label>
                    {!!Form::text('imei', $item->imei, ['class' => 'form-control', 'placeholder' => trans('front.imei_placeholder')] + ( ! Auth::user()->can('edit', $item, 'imei') ? ['disabled' => 'disabled'] : []) )!!}
                </div>
            @endif

            @if (isAdmin() && Auth::user()->can('view', $item, 'expiration_date'))
                <div class="form-group">
                    {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}
                    <div class="input-group">
                        <div class="checkbox input-group-btn">
                            {!! Form::hidden('enable_expiration_date', 0) !!}
                            {!! Form::checkbox('enable_expiration_date', 1, ($item->hasExpireDate()), Auth::user()->can('edit', $item, 'expiration_date') ? [] : ['disabled' => 'disabled']) !!}
                            {!! Form::label(null) !!}
                        </div>
                        {!! Form::text(
                            'expiration_date',
                            $item->hasExpireDate()
                                ? Formatter::time()->convert($item->expiration_date)
                                : null,
                            [
                                'class' => 'form-control datetimepicker',
                                'disabled' => 'disabled'
                            ])
                        !!}
                    </div>
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
            @if(Auth::user()->can('view', $item, 'device_type_id'))
                <div class="form-group">
                    {!!Form::label('device_type_id', trans('validation.attributes.device_type_id').':')!!}
                    {!!Form::select('device_type_id', $device_types, $item->device_type_id, ['class' => 'form-control'] + ( ! Auth::user()->can('edit', $item, 'device_type_id') ? ['disabled' => 'disabled'] : []))!!}
                </div>
            @endif

            @if(Auth::user()->can('view', $item, 'authentication'))
                <div class="form-group">
                    {!! Form::label('authentication', trans('validation.attributes.authentication').':') !!}
                    {!! Form::text('authentication',
                        $item->authentication,
                        ['class' => 'form-control'] + (Auth::user()->can('edit', $item, 'authentication') ? [] : ['disabled']))
                    !!}
                </div>
            @endif

            <div class="row">
                <div class="col-sm-6">
                    @if(Auth::user()->can('view', $item, 'sim_number'))
                        <div class="form-group">
                            {!!Form::label('sim_number', trans('validation.attributes.sim_number').':')!!}
                            {!!Form::text('sim_number', $item->sim_number, ['class' => 'form-control'] + ( ! Auth::user()->can('edit', $item, 'sim_number') ? ['disabled' => 'disabled'] : []))!!}
                        </div>
                    @endif

                    @if(settings('plugins.sim_blocking.status') && Auth::user()->can('edit', new \Tobuli\Entities\Device(), 'msisdn'))
                        <div class="form-group">
                            {!! Form::label('msisdn', trans('validation.attributes.msisdn').':') !!}
                            {!! Form::text('msisdn',
                                $item->msisdn,
                                ['class' => 'form-control'] + (Auth::user()->can('edit', new \Tobuli\Entities\Device(), 'msisdn') ? [] : ['disabled']))
                            !!}
                        </div>
                    @endif

                    @if($additional_fields_on && Auth::user()->can('view', $item, 'sim_activation_date'))
                        <div class="form-group">
                            {!!Form::label('sim_activation_date', trans('validation.attributes.sim_activation_date').':')!!}
                            {!!Form::text('sim_activation_date', $item->sim_activation_date == '0000-00-00' ? null : $item->sim_activation_date, ['class' => 'form-control datepicker'] + ( ! Auth::user()->can('edit', $item, 'sim_activation_date') ? ['disabled' => 'disabled'] : []))!!}
                        </div>
                    @endif

                    @if($additional_fields_on && Auth::user()->can('view', $item, 'sim_expiration_date'))
                        <div class="form-group">
                            {!!Form::label('sim_expiration_date', trans('validation.attributes.sim_expiration_date').':')!!}
                            {!!Form::text('sim_expiration_date', $item->sim_expiration_date == '0000-00-00' ? null : $item->sim_expiration_date, ['class' => 'form-control datepicker'] + ( ! Auth::user()->can('edit', $item, 'sim_expiration_date') ? ['disabled' => 'disabled'] : []))!!}
                        </div>
                    @endif
                    <div class="form-group">
                        {!!Form::label('vin', trans('validation.attributes.vin').':')!!}
                        {!!Form::text('vin', $item->vin, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('device_model', trans('validation.attributes.device_model').':')!!}
                        {!!Form::text('device_model', $item->device_model, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-sm-6">
                    @if($additional_fields_on && Auth::user()->can('view', $item, 'installation_date'))
                        <div class="form-group">
                            {!!Form::label('installation_date', trans('validation.attributes.installation_date').':')!!}
                            {!!Form::text('installation_date', $item->installation_date == '0000-00-00' ? NULL : $item->installation_date, ['class' => 'form-control datepicker'] + ( ! Auth::user()->can('edit', $item, 'installation_date') ? ['disabled' => 'disabled'] : []))!!}
                        </div>
                    @endif
                    <div class="form-group">
                        {!!Form::label('plate_number', trans('validation.attributes.plate_number').':')!!}
                        {!!Form::text('plate_number', $item->plate_number, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('registration_number', trans('validation.attributes.registration_number').':')!!}
                        {!!Form::text('registration_number', $item->registration_number, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('object_owner', trans('validation.attributes.object_owner').':')!!}
                        {!!Form::text('object_owner', $item->object_owner, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('file', 'Imagen de insatalacion:') !!}
                        {!! Form::file('file', ['class' => 'form-control']) !!}
                        <div class="text-right">
                            <a target="_blank" href="{{ asset($item->file_path) }}">Click aqui para ver la Imagen</a>
                        </div>
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
            @if (config('addon.device_tracker_app_login'))
                <div class="form-group">
                    <div class="checkbox-inline">
                        {!! Form::hidden('app_tracker_login', 0) !!}
                        {!! Form::checkbox('app_tracker_login', 1, $item->app_tracker_login) !!}
                        {!! Form::label(null, trans('validation.attributes.app_tracker_login')) !!}
                    </div>
                </div>
            @endif
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('gprs_templates_only', 0) !!}
                    {!! Form::checkbox('gprs_templates_only', 1, $item->gprs_templates_only) !!}
                    {!! Form::label('gprs_templates_only', trans('validation.attributes.gprs_templates_only') ) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!!Form::label('fuel_measurement_id', trans('validation.attributes.fuel_measurement_type').':')!!}
                        {!!Form::select('fuel_measurement_id', $device_fuel_measurements_select, $item->fuel_measurement_id, ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fuel_quantity">
                            <span class="fuel_title"></span> {!!trans('front.per')!!} <span class="distance_title"></span>:
                        </label>
                        {!!Form::text('fuel_quantity', $item->fuel_quantity, ['class' => 'form-control', 'placeholder' => '0.00', 'id' => 'fuel_quantity'])!!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fuel_price">{!!trans('front.cost_for')!!} <span class="cost_title"></span>:</label>
                        {!!Form::text('fuel_price', $item->fuel_price, ['class' => 'form-control', 'placeholder' => '0.00', 'id' => 'fuel_price'])!!}
                    </div>
                </div>
            </div>
            @if(Auth::user()->can('view', $item, 'forward'))
                <div class="form-group">
                    {!! Form::label(null, trans('validation.attributes.forward').':') !!}
                    <div class="input-group">
                        <div class="checkbox input-group-btn">
                            {!! Form::hidden('forward[active]', 0) !!}
                            {!! Form::checkbox('forward[active]', 1, \Illuminate\Support\Arr::get($item->forward, 'active')) !!}
                            {!! Form::label(null) !!}
                        </div>
                        {!! Form::text('forward[ip]', \Illuminate\Support\Arr::get($item->forward, 'ip'), array_merge(['class' => 'form-control', 'placeholder' => '10.0.0.0:6000'], (Auth::user()->can('edit', $item, 'forward')) ? [] : ['readonly' => true])) !!}
                        <div class="input-group-addon">
                            <div class="checkbox-inline">
                                {!! Form::radio('forward[protocol]', 'TCP', \Illuminate\Support\Arr::get($item->forward, 'protocol') != 'UDP') !!}
                                {!! Form::label(null, 'TCP') !!}
                            </div>
                            <div class="checkbox-inline">
                                {!! Form::radio('forward[protocol]', 'UDP', \Illuminate\Support\Arr::get($item->forward, 'protocol') == 'UDP') !!}
                                {!! Form::label(null, 'UDP') !!}
                            </div>
                        </div>
                    </div>
                    <small>{!!trans('front.forward_semicolon')!!}</small>
                </div>
            @endif
            <div class="form-group">
                {!!Form::label('timezone_id', trans('validation.attributes.time_adjustment').':')!!}
                {!!Form::select('timezone_id', $timezones, !is_null($timezone_id) ? $timezone_id : 0, ['class' => 'form-control'])!!}
                <small>{!!trans('front.by_default_time')!!}</small>
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
                @include('Frontend.Sensors.index')
            </div>
            @if (isAdmin())
                <div class="form-group">
                    {!! Form::label('sensor_group_id', trans('validation.attributes.sensor_group_id').':') !!}
                    {!! Form::select('sensor_group_id', $sensor_groups, null, ['class' => 'form-control']) !!}
                </div>
            @endif
        </div>
        <div id="device-form-services" class="tab-pane">
            <div class="action-block">
                <a href="javascript:" class="btn btn-action" data-url="{!!route('services.create', $item->id)!!}"
                   data-modal="services_create" type="button">
                    <i class="icon add"></i> {{ trans('front.add_service') }}
                </a>
            </div>
            <div data-table>
                @include('Frontend.Services.table')
            </div>
        </div>
        <div id="device-form-accuracy" class="tab-pane">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('valid_by_avg_speed', 0) !!}
                    {!! Form::checkbox('valid_by_avg_speed', 1, $item->valid_by_avg_speed) !!}
                    {!! Form::label('valid_by_avg_speed', trans('front.valid_by_avg_speed')) !!}
                </div>
            </div>
            <div class="form-group">
                {!!Form::label('engine_hours', trans('validation.attributes.ignition_detection').':')!!}
                {!!Form::select('engine_hours', $engine_hours, $item->engine_hours, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group ignition_detection_engine">
                {!!Form::label('detect_engine', trans('validation.attributes.detect_engine').':')!!}
                {!!Form::select('detect_engine', $detect_engine, $item->detect_engine, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('detect_speed', trans('validation.attributes.detect_speed').':')!!}
                {!!Form::select('detect_speed', $detect_speed, $item->detect_speed, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_moving_speed', trans('validation.attributes.min_moving_speed').' ('.trans('front.affects_stops_track',['default'=>6]).'):')!!}
                {!!Form::text('min_moving_speed', $item->min_moving_speed, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_fuel_fillings', trans('validation.attributes.min_fuel_fillings').' ('.trans('front.default_value',['default'=>10]).'):')!!}
                {!!Form::text('min_fuel_fillings', $item->min_fuel_fillings, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('min_fuel_thefts', trans('validation.attributes.min_fuel_thefts').' ('.trans('front.default_value',['default'=>10]).'):')!!}
                {!!Form::text('min_fuel_thefts', $item->min_fuel_thefts, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!! Form::label('fuel_detect_sec_after_stop', trans('validation.attributes.fuel_detect_sec_after_stop').':') !!}
                <div class="input-group">
                    <div class="checkbox input-group-btn">
                        {!! Form::hidden('fuel_detect_sec_after_stop') !!}
                        {!! Form::checkbox('enable_fuel_detect_sec_after_stop', 1, (bool)$item->fuel_detect_sec_after_stop, ['data-disabler' => 'select[name="fuel_detect_sec_after_stop"];disable']) !!}
                        {!! Form::label(null) !!}
                    </div>
                    {!! Form::select('fuel_detect_sec_after_stop', $fuel_detect_sec_after_stop_options, $item->fuel_detect_sec_after_stop, ['class' => 'form-control']) !!}
                </div>
            </div>
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
        @if(Auth::User()->perm('device_camera', 'view'))
            <div id="device-form-cameras" class="tab-pane">
                <div class="action-block">
                    <a href="javascript:" class="btn btn-action" data-url="{!!route('device_camera.create', $item->id)!!}"
                    data-modal="device_camera_create" type="button">
                        <i class="icon add"></i> {{ trans('front.add_camera') }}
                    </a>
                </div>
                <div data-table>
                    @include('Frontend.DeviceMedia.partials.cameras.index')
                </div>
            </div>
        @endif
        @if (Auth::user()->can('view', $item, 'custom_fields') && $item->hasCustomFields())
            <div id="device-custom-fields" class="tab-pane">
                @include('Frontend.CustomFields.panel')
            </div>
        @endif
    </div>
    {!!Form::close()!!}
    <script>
        $(document).ready(function () {

            var measurements = {!!json_encode($device_fuel_measurements)!!};

            $(document).on('change', '#devices_edit select[name="fuel_measurement_id"]', function () {
                var val = $(this).val();

                $.each(measurements, function (index, value) {
                    if (value.id == val) {
                        $('.distance_title').html(value.distance_title);
                        $('.fuel_title').html(value.fuel_title);
                        $('.cost_title').html(value.cost_title);
                    }
                });
            });

            $(document).on('change', '#devices_edit input[name="enable_expiration_date"]', function () {
                if ($(this).prop('checked'))
                    $('input[name="expiration_date"]').removeAttr('disabled');
                else
                    $('input[name="expiration_date"]').attr('disabled', 'disabled');
            });

            $(document).on('change', '#devices_edit input[name="forward[active]"]', function () {
                if ($(this).prop('checked'))
                    $('input[name^="forward["]:not([name="forward[active]"])').removeAttr('disabled');
                else
                    $('input[name^="forward["]:not([name="forward[active]"])').attr('disabled', 'disabled');
            });


            $('select[name="device_icons_type"]').trigger('change');

            $('#devices_edit input[name="forward[active]"]').trigger('change');

            $('#devices_edit select[name="engine_hours"]').trigger('change');

            $('#devices_edit input[name="enable_expiration_date"]').trigger('change');

            $('#devices_edit select[name="fuel_measurement_id"]').trigger('change');
        });

        tables.set_config('device-form-services', {
            url: '{!!route('services.table', $item->id)!!}'
        });

        function services_create_modal_callback() {
            tables.get('device-form-services');
        }

        function services_edit_modal_callback() {
            tables.get('device-form-services');
        }

        function services_destroy_modal_callback() {
            tables.get('device-form-services');
        }

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

        tables.set_config('device-form-cameras', {
            url: '{!!route('device_camera.index', $item->id)!!}'
        });

        function device_camera_create_modal_callback() {
            tables.get('device-form-cameras');
        }

        function device_camera_edit_modal_callback() {
            tables.get('device-form-cameras');
        }

        function device_camera_destroy_modal_callback() {
            tables.get('device-form-cameras');
        }

        function set_engine_hours_modal_callback() {
            app.devices.loadData({{ $item->id }});
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