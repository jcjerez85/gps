@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon setup"></i> {{ trans('front.setup') }}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#setup-form-main" role="tab" data-toggle="tab">{!!trans('front.main')!!}</a></li>
        <li><a href="#setup-form-object-groups" role="tab" data-toggle="tab" data-url="{{ route('devices_groups.index') }}">{!!trans('front.object_groups')!!}</a></li>
        @if (Auth::User()->perm('drivers', 'view'))
        <li><a href="#setup-form-drivers" role="tab" data-toggle="tab" data-url="{{ route('user_drivers.index') }}">{!!trans('front.drivers')!!}</a></li>
        @endif
        @if (Auth::User()->perm('custom_events', 'view'))
        <li><a href="#setup-form-events" role="tab" data-toggle="tab" data-url="{{ route('custom_events.index') }}">{!!trans('front.events')!!}</a></li>
        @endif
        @if (Auth::User()->perm('sms_gateway', 'view'))
        <li><a href="#setup-form-sms-gateway" role="tab" data-toggle="tab">{!!trans('validation.attributes.permission_to_use_sms_gateway')!!}</a></li>
        @endif
        @if (Auth::User()->perm('user_sms_templates', 'view'))
        <li><a href="#setup-form-sms-templates" role="tab" data-toggle="tab" data-url="{{ route('user_sms_templates.index') }}">{!!trans('front.sms_templates')!!}</a></li>
        @endif
        @if (Auth::User()->perm('user_gprs_templates', 'view'))
        <li><a href="#setup-form-gprs-templates" role="tab" data-toggle="tab" data-url="{{ route('user_gprs_templates.index') }}">{!!trans('front.gprs_templates')!!}</a></li>
        @endif
        <li><a href="#setup-form-widgets" role="tab" data-toggle="tab">{!!trans('front.widgets')!!}</a></li>
        <li><a href="#setup-form-dashboard" role="tab" data-toggle="tab">{!!trans('front.dashboard')!!}</a></li>
        <li><a href="#setup-form-client" role="tab" data-toggle="tab">{!!trans('validation.attributes.client')!!}</a></li>
        @if (Auth::User()->perm('media_categories', 'view'))
        <li><a href="#setup-form-categories" role="tab" data-toggle="tab" data-url="{{ route('media_categories.index') }}">{!! trans('front.media_categories') !!}</a></li>
        @endif
        @if (config('auth.secondary_credentials') && Auth::user()->isMainLogin())
        <li><a href="#setup-secondary-credentials" role="tab" data-toggle="tab" data-url="{{ route('secondary_credentials.index') }}">{!! trans('front.secondary_credentials') !!}</a></li>
        @endif
    </ul>

    {!!Form::open(['route' => 'my_account_settings.update', 'method' => 'PUT', ''])!!}
    {!!Form::hidden('id', $item['id'])!!}

    <div class="tab-content">
        <div id="setup-form-main" class="tab-pane active">
            <div class="form-group">
                {!!Form::label('unit_of_distance', trans('validation.attributes.unit_of_distance').':')!!}
                {!!Form::select('unit_of_distance', $units_of_distance, $item['unit_of_distance'], ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('unit_of_capacity', trans('validation.attributes.unit_of_capacity').':')!!}
                {!!Form::select('unit_of_capacity', $units_of_capacity, $item['unit_of_capacity'], ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('unit_of_altitude', trans('validation.attributes.unit_of_altitude').':')!!}
                {!!Form::select('unit_of_altitude', $units_of_altitude, $item['unit_of_altitude'], ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!!Form::label('duration_format', trans('validation.attributes.duration_format').':')!!}
                {!!Form::select('duration_format', $duration_formats, $item['duration_format'] ?? null, ['class' => 'form-control'])!!}
            </div>
            <div class="form-group">
                {!! Form::label('week_start_day', trans('validation.attributes.week_start_day').':') !!}
                {!! Form::select('week_start_day', $week_start_days, (isset($item['week_start_day']) ? $item['week_start_day'] : null), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!!Form::label('timezone_id', trans('validation.attributes.timezone_id').':')!!}
                {!!Form::select('timezone_id', $timezones, $item['timezone_id'], ['class' => 'form-control'])!!}
            </div>

            <hr>

            <h4>{{ trans('validation.attributes.daylight_saving_time') }}</h4>
            <div class="form-group">
                {!! Form::label('dst_type', trans('validation.attributes.dst_type').':') !!}
                {!! Form::select('dst_type', $dst_types, isset($user_dst->type) ? $user_dst->type : NULL, ['class' => 'form-control']) !!}
            </div>
            <div class="row" id="dst_exact">
                <div class="col-xs-6">
                    <div class="form-group">
                        {!! Form::label('date_from', trans('validation.attributes.date_from').':') !!}
                        {!! Form::text('date_from', isset($user_dst->date_from) ? $user_dst->date_from : NULL, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        {!! Form::label('date_to', trans('validation.attributes.date_to').':') !!}
                        {!! Form::text('date_to', isset($user_dst->date_to) ? $user_dst->date_to : NULL, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div id="dst_other">
                <div class="form-group">
                    {!! Form::label('date_from', trans('front.from').':') !!}
                    <div class="row">
                        <div class="col-xs-4">
                            {!! Form::select('month_from', $months, isset($user_dst->month_from) ? $user_dst->month_from : NULL, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-2">
                            {!! Form::select('week_pos_from', $week_pos, isset($user_dst->week_pos_from) ? $user_dst->week_pos_from : NULL, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-4">
                            {!! Form::select('week_day_from', $weekdays, isset($user_dst->week_day_from) ? $user_dst->week_day_from : NULL, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-2">
                            {!! Form::text('time_from', isset($user_dst->time_from) ? $user_dst->time_from : NULL, ['class' => 'form-control', 'placeholder' => trans('front.time')]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('date_to', trans('front.to').':') !!}
                    <div class="row">
                        <div class="col-xs-4">
                            {!! Form::select('month_to', $months, isset($user_dst->month_to) ? $user_dst->month_to : NULL, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-2">
                            {!! Form::select('week_pos_to', $week_pos, isset($user_dst->week_pos_to) ? $user_dst->week_pos_to : NULL, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-4">
                            {!! Form::select('week_day_to', $weekdays, isset($user_dst->week_day_to) ? $user_dst->week_day_to : NULL, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-2">
                            {!! Form::text('time_to', isset($user_dst->time_to) ? $user_dst->time_to : NULL, ['class' => 'form-control', 'placeholder' => trans('front.time')]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div id="dst_automatic">
                <div class="form-group">
                    {!! Form::label('dst_country_id', trans('front.country').':') !!}
                    {!! Form::select('dst_country_id', $dst_countries, isset($user_dst->country_id) ? $user_dst->country_id : NULL, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
                </div>
            </div>
        </div>

        <div id="setup-form-object-groups" class="tab-pane"></div>

        <div id="setup-form-drivers" class="tab-pane"></div>

        <div id="setup-form-events" class="tab-pane"></div>

        <div id="setup-form-sms-gateway" class="tab-pane">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::checkbox('sms_gateway', 1, $item['sms_gateway']) !!}
                    {!! Form::label('sms_gateway', trans('front.enable_sms_gateway')) !!}
                </div>
            </div>
            <div class="form-group">
                {!!Form::label('request_method', trans('validation.attributes.request_method').':')!!}
                {!!Form::select('request_method', $request_method_select, $item['sms_gateway_params']['request_method'], ['class' => 'form-control'])!!}
            </div>
            <div class="request-method request-method-post">
                <div class="form-group">
                    {!!Form::label('encoding', trans('validation.attributes.encoding').':')!!}
                    {!!Form::select('encoding', $encoding_select, $item['sms_gateway_params']['encoding'], ['class' => 'form-control'])!!}
                </div>
            </div>
            <div class="request-method request-method-get request-method-post">
                <div class="form-group">
                    {!!Form::label('authentication', trans('validation.attributes.authentication').':')!!}
                    {!!Form::select('authentication', $authentication_select, $item['sms_gateway_params']['authentication'], ['class' => 'form-control'])!!}
                </div>
                <div class="form-group sms-gateway-auth">
                    {!!Form::label('username', trans('validation.attributes.username').':')!!}
                    {!!Form::text('username', $item['sms_gateway_params']['username'], ['class' => 'form-control'])!!}
                </div>
                <div class="form-group sms-gateway-auth">
                    {!!Form::label('password', trans('validation.attributes.password').':')!!}
                    {!!Form::password('password', ['class' => 'form-control'])!!}
                </div>
                <div class="form-group">
                    {!!Form::label('custom_headers', trans('validation.attributes.sms_gateway_headers').':')!!}
                    {!!Form::textarea('custom_headers', $item['sms_gateway_params']['custom_headers'], ['class' => 'form-control', 'rows' => 2])!!}
                    <small>Colon seperated list ( e.g. Accept: text/plain; Accept-Language: en-US; ) </small>
                </div>
                <div class="form-group">
                    {!!Form::label('sms_gateway_url', trans('validation.attributes.sms_gateway_url').':')!!}
                    {!!Form::textarea('sms_gateway_url', $item['sms_gateway_url'], ['class' => 'form-control', 'rows' => 3])!!}
                </div>
                <div class="alert alert-info">
                    {!!trans('front.sms_gateway_text')!!}
                </div>

                <button type="button" class="btn btn-info btn-xs" data-url="{!!route('sms_gateway.test_sms')!!}" data-modal="send_test_sms">{!!trans('front.send_test_sms')!!}</button>
            </div>
            <div class="request-method request-method-app">
                <div class="form-group">
                    <button type="button" class="btn btn-danger btn-xs" onClick="app.clearQueue();">{!!trans('front.clear_queue')!!}</button>
                    <button type="button" class="btn btn-info btn-xs" data-url="{!!route('sms_gateway.test_sms')!!}" data-modal="send_test_sms">{!!trans('front.send_test_sms')!!}</button>
                </div>
                <div class="form-group">
                    <small>{!!trans('front.sms_in_queue')!!}: <span class="sms_queue_count">{!!$sms_queue_count!!}</span></small><br>
                    <small>{!!trans('front.app_last_connection')!!}: {!! Formatter::time()->human(Auth::User()->sms_gateway_app_date) !!}</small><br>
                    <small>{!!trans('front.sms_deletion_after')!!}</small>
                </div>
            </div>
            <div class="request-method request-method-plivo">
                <div class="form-group">
                    {!! Form::label('auth_id', trans('validation.attributes.auth_id').':') !!}
                    {!! Form::text('auth_id', (isset($item['sms_gateway_params']['auth_id']) ? $item['sms_gateway_params']['auth_id'] : null), ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('auth_token', trans('validation.attributes.auth_token').':') !!}
                    {!! Form::text('auth_token', (isset($item['sms_gateway_params']['auth_token']) ? $item['sms_gateway_params']['auth_token'] : null), ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('senders_phone', trans('validation.attributes.senders_phone').':') !!}
                    {!! Form::text('senders_phone', (isset($item['sms_gateway_params']['senders_phone']) ? $item['sms_gateway_params']['senders_phone'] : null), ['class' => 'form-control']) !!}
                </div>

                <button type="button" class="btn btn-info btn-xs" data-url="{{ route('sms_gateway.test_sms') }}" data-modal="send_test_sms">{{ trans('front.send_test_sms') }}</button>
            </div>
            <div class="request-method request-method-server">
                <button type="button" class="btn btn-info btn-xs" data-url="{{ route('sms_gateway.test_sms') }}" data-modal="send_test_sms">{{ trans('front.send_test_sms') }}</button>
            </div>
        </div>

        <div id="setup-form-sms-templates" class="tab-pane"></div>
        <div id="setup-form-gprs-templates" class="tab-pane"></div>

        <div id="setup-form-widgets" class="tab-pane">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::checkbox('default_widgets', 1, !empty($widgets['default'])) !!}
                    {!! Form::label('default_widgets', trans('front.default') . ' ' .trans('front.widgets')) !!}
                </div>

                <hr>

                <div class="checkbox">
                    {!! Form::checkbox('widgets[status]', 1, !empty($widgets['status'])) !!}
                    {!! Form::label('widgets[status]', trans('front.enable_widgets')) !!}
                </div>

                <div id="setup-widgets-list" class="row">
                    <?php $widgets_list = config('lists.widgets'); ?>

                    @if ( !empty($widgets['list']) )
                        @foreach($widgets['list'] as $widget)
                            @if ( !empty($widgets_list[$widget]) )
                                <div class="widget-col col-xs-12 col-sm-4 col-md-3">
                                    <div>
                                        <div class="checkbox-inline">
                                            {!! Form::checkbox('widgets[list][]', $widget, true) !!}
                                            {!! Form::label(null, $widgets_list[$widget]) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    @foreach($widgets_list as $widget => $title)
                        @if ( empty($widgets['list']) )
                            <div class="widget-col col-xs-12 col-sm-4 col-md-3">
                                <div>
                                    <div class="checkbox-inline">
                                        {!! Form::checkbox('widgets[list][]', $widget, true) !!}
                                        {!! Form::label(null, $title) !!}
                                    </div>
                                </div>
                            </div>
                        @elseif ( !in_array($widget, $widgets['list']) )
                            <div class="widget-col col-xs-12 col-sm-4 col-md-3">
                                <div>
                                    <div class="checkbox-inline">
                                        {!! Form::checkbox('widgets[list][]', $widget, false) !!}
                                        {!! Form::label(null, $title) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div id="setup-form-dashboard" class="tab-pane">
            <div class="checkbox">
                {!! Form::hidden('dashboard[enabled]', 0) !!}
                {!! Form::checkbox('dashboard[enabled]', 1, $dashboard['enabled']) !!}
                {!! Form::label('dashboard[enabled]', trans('front.enable_dashboard')) !!}
            </div>

            <hr>
            <h5><b>{{ trans('front.widgets') }}</b></h5>

            <div class="row">
                @foreach($dashboard['blocks'] as $block => $config)
                    <div class="col-md-3">
                        <div class="dashboard-widget clearfix">
                            <div class="pull-left">
                                <div class="checkbox-inline">
                                    {!! Form::hidden("dashboard[blocks][$block][enabled]", 0) !!}
                                    {!! Form::checkbox("dashboard[blocks][$block][enabled]", 1, $config['enabled']) !!}
                                    {!! Form::label("dashboard[blocks][$block][enabled]", trans("front.$block")) !!}
                                </div>
                            </div>

                            <div class="pull-right">
                                @if(View::exists("Frontend.Dashboard.Options.$block"))
                                    <div class="btn-group dropleft droparrow" data-position="fixed">
                                        <i class="btn icon options"
                                           data-toggle="dropdown"
                                           data-position="fixed"
                                           aria-haspopup="true"
                                           aria-expanded="false"></i>

                                        <div class="dropdown-menu">
                                            @include("Frontend.Dashboard.Options.$block", ['options' => $config['options']])
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if (Auth::user()->perm('user.client_id', 'view'))
            @php
                $clientDisabled = !Auth::user()->perm('user.client_id', 'edit');
                $companyDisabled = $clientDisabled;
            @endphp

            <div id="setup-form-client" class="tab-pane row">
                <div class="form-group">
                    <div class="col-sm-12">
                        @php /** @var \Tobuli\Entities\Client $client */ @endphp
                        <h4>{{ trans('validation.attributes.client') }}</h4>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        {!! Form::label("client[first_name]", trans("validation.attributes.first_name") . ':') !!}
                        {!! Form::text("client[first_name]", $client->first_name, ['class' => 'form-control', 'disabled' => $clientDisabled]) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::label("client[last_name]", trans("validation.attributes.last_name") . ':') !!}
                        {!! Form::text("client[last_name]", $client->last_name, ['class' => 'form-control', 'disabled' => $clientDisabled]) !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        {!! Form::label("client[personal_code]", trans("validation.attributes.personal_code") . ':') !!}
                        {!! Form::text("client[personal_code]", $client->personal_code, ['class' => 'form-control', 'disabled' => $clientDisabled]) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::label("client[birth_date]", trans("validation.attributes.birth_date") . ':') !!}
                        {!! Form::text("client[birth_date]", $client->birth_date, ['class' => 'form-control datepicker', 'disabled' => $clientDisabled]) !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        {!! Form::label("client[address]", trans("validation.attributes.address") . ':') !!}
                        {!! Form::text("client[address]", $client->address, ['class' => 'form-control', 'disabled' => $clientDisabled]) !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        @php /** @var \Tobuli\Entities\Company $company */ @endphp
                        <h4>{{ trans('validation.attributes.company_id') }}</h4>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("company[name]", trans("validation.attributes.name") . ':') !!}
                            {!! Form::text("company[name]", $company->name ?? null, ['class' => 'form-control', 'disabled' => $companyDisabled]) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("company[registration_code]", trans("validation.attributes.registration_code") . ':') !!}
                            {!! Form::text("company[registration_code]", $company->registration_code ?? null, ['class' => 'form-control', 'disabled' => $companyDisabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("company[vat_number]", trans("validation.attributes.vat_number") . ':') !!}
                            {!! Form::text("company[vat_number]", $company->vat_number ?? null, ['class' => 'form-control', 'disabled' => $companyDisabled]) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("company[address]", trans("validation.attributes.address") . ':') !!}
                            {!! Form::text("company[address]", $company->address ?? null, ['class' => 'form-control', 'disabled' => $companyDisabled]) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div id="setup-form-categories" class="tab-pane"></div>

        <div id="setup-secondary-credentials" class="tab-pane"></div>
    </div>
    {!!Form::close()!!}

    <script>
        var $sms_gateway_container = $('#setup-form-sms-gateway');
        var $settings_container = $('#my_account_settings_edit');

        $settings_container.on('change', 'select[name="dst_type"]', function () {
            $('div[id^="dst_"]').slideUp(200);

            if ($(this).val() == 'none') {
                return;
            }

            $('#dst_' + $(this).val()).slideDown(200);
        });

        $settings_container.on('change', 'input[name="dst"]', function () {
            if (!$(this).prop('checked')) {
                $('input[name="date_from"]', $settings_container).attr('disabled', 'disabled');
                $('input[name="date_to"]', $settings_container).attr('disabled', 'disabled');
            }
            else {
                $('input[name="date_from"]', $settings_container).removeAttr('disabled');
                $('input[name="date_to"]', $settings_container).removeAttr('disabled');
            }
        });

         $('input[name="date_from"]', $settings_container).datetimepicker({
             changeYear: false,
             format: 'mm-dd hh:ii',
             closeOnDateSelect: true,
             weekStart: app.settings.weekStart
         }).on('monthUpdate', titleRemoveYear);

         $('input[name="date_to"]', $settings_container).datetimepicker({
             changeYear: false,
             format: 'mm-dd hh:ii',
             closeOnDateSelect: true,
             weekStart: app.settings.weekStart
         }).on('monthUpdate', titleRemoveYear);


        $settings_container.on('change', 'input[name="default_widgets"]', function () {
            if ($(this).prop('checked')) {
                $('input[name^="widgets"]', $settings_container).attr('disabled', 'disabled');
            }
            else {
                $('input[name^="widgets"]', $settings_container).removeAttr('disabled');
            }
        });

        $sms_gateway_container.on('change', 'select[name="request_method"]', function () {
            dd('select[name="request_method"]');
            $('.request-method', $sms_gateway_container).hide();
            $('.request-method-' + $(this).val(), $sms_gateway_container).show();
        });

        $sms_gateway_container.on('change', 'select[name="authentication"]', function () {
            if ($(this).val() == 1)
                $('.sms-gateway-auth', $sms_gateway_container).show();
            else
                $('.sms-gateway-auth', $sms_gateway_container).hide();
        });

        $('input[name="dst"]', $settings_container).trigger('change');
        $('select[name="dst_type"]', $settings_container).trigger('change');
        $('select[name="request_method"]', $sms_gateway_container).trigger('change');
        $('select[name="authentication"]', $sms_gateway_container).trigger('change');
        $('input[name="default_widgets"]', $settings_container).trigger('change');

        $( "#setup-widgets-list" ).sortable();
    </script>
@stop