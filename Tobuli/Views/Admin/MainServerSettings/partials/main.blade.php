<div class="panel panel-default">

    <div class="panel-heading">
        <div class="panel-title"><i class="icon setup"></i> {{ trans('front.main_server_settings') }}</div>
    </div>

    <div class="panel-body">
        {!! Form::open(array('route' => 'admin.main_server_settings.save', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'main-settings-form')) !!}

        <div class="form-group">
            {!! Form::label('server_name', trans('validation.attributes.server_name'), ['class' => 'col-xs-12 control-label"']) !!}
            <div class="col-xs-12">
                {!! Form::text('server_name', $settings['server_name'], ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('server_description', trans('validation.attributes.server_description'), ['class' => 'col-xs-12 control-label"']) !!}
            <div class="col-xs-12">
                {!! Form::text('server_description', $settings['server_description'], ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                {!! Form::label(null, trans('validation.attributes.default_maps').':') !!}
                <div class="checkboxes">
                    {!! Form::hidden('available_maps') !!}

                    @foreach ($maps as $id => $title)
                        <div class="checkbox">
                            {!! Form::checkbox('available_maps[]', $id, in_array($id, $settings['available_maps'])) !!}
                            {!! Form::label(null, $title) !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="map-setting map-setting-1 map-setting-3 map-setting-4 map-setting-5">
            <div class="form-group">
                {!! Form::label('google_maps_key', trans('validation.attributes.google_maps_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                <div class="col-xs-12 col-sm-8">
                    {!! Form::text('google_maps_key', settings('main_settings.google_maps_key'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="map-setting map-setting-10">
            <div class="form-group">
                {!! Form::label('here_api_key', trans('validation.attributes.here_api_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                <div class="col-xs-12 col-sm-8">
                    {!! Form::text('here_api_key', settings('main_settings.here_api_key'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="map-setting map-setting-14 map-setting-15 map-setting-16">
            <div class="form-group">
                {!! Form::label('mapbox_access_token', trans('validation.attributes.mapbox_access_token'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                <div class="col-xs-12 col-sm-8">
                    {!! Form::text('mapbox_access_token', settings('main_settings.mapbox_access_token'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="map-setting map-setting-7 map-setting-8 map-setting-9">
            <div class="form-group">
                {!! Form::label('bing_maps_key', trans('validation.attributes.bing_maps_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                <div class="col-xs-12 col-sm-8">
                    {!! Form::text('bing_maps_key', settings('main_settings.bing_maps_key'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="map-setting map-setting-17 map-setting-18 map-setting-19">
            <div class="form-group">
                {!! Form::label('maptiler_key', trans('validation.attributes.maptiler_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                <div class="col-xs-12 col-sm-8">
                    {!! Form::text('maptiler_key', settings('main_settings.maptiler_key'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="map-setting map-setting-21">
            <div class="form-group">
                {!! Form::label('openmaptiles_url', trans('validation.attributes.openmaptiles_url'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                <div class="col-xs-12 col-sm-8">
                    {!! Form::text('openmaptiles_url', settings('main_settings.openmaptiles_url'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="map-setting map-setting-26 map-setting-27">
            <div class="form-group">
                {!! Form::label('tomtom_key', trans('validation.attributes.tomtom_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                <div class="col-xs-12 col-sm-8">
                    {!! Form::text('tomtom_key', settings('main_settings.tomtom_key'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <hr>

        <div class="form-group">
            {!! Form::label('default_language', trans('validation.attributes.default_language'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                <select name="default_language" class="form-control" data-icon="icon globe">
                @foreach($langs as $lang)
                    <option value="{{ $lang['key'] }}" {{ $lang['key'] == $settings['default_language'] ? 'selected="selected"' : ''}} {{ empty($lang['active']) ? 'disabled="disabled"' : ''}}>
                    {{ $lang['title'] }}
                    </option>
                @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('default_date_format', trans('validation.attributes.default_date_format'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('default_date_format', $date_formats, $settings['default_date_format'], ['class' => 'form-control', 'data-icon' => 'icon calendar']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('default_time_format', trans('validation.attributes.default_time_format'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('default_time_format', $time_formats, $settings['default_time_format'], ['class' => 'form-control', 'data-icon' => 'icon calendar']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('default_duration_format', trans('validation.attributes.default_duration_format'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('default_duration_format', $duration_formats, $settings['default_duration_format'], ['class' => 'form-control', 'data-icon' => 'icon calendar']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('default_unit_of_distance', trans('validation.attributes.default_unit_of_distance'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('default_unit_of_distance', $units_of_distance, $settings['default_unit_of_distance'], ['class' => 'form-control', 'data-icon' => 'icon unit-distance']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('default_unit_of_capacity', trans('validation.attributes.default_unit_of_capacity'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('default_unit_of_capacity', $units_of_capacity, $settings['default_unit_of_capacity'], ['class' => 'form-control', 'data-icon' => 'icon unit-capacity']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('default_unit_of_altitude', trans('validation.attributes.default_unit_of_altitude'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('default_unit_of_altitude', $units_of_altitude, $settings['default_unit_of_altitude'], ['class' => 'form-control', 'data-icon' => 'icon unit-altitude']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('default_object_online_timeout', trans('validation.attributes.default_object_online_timeout'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('default_object_online_timeout', $object_online_timeouts, $settings['default_object_online_timeout'], ['class' => 'form-control', 'data-icon' => 'icon time', 'data-size' => '10']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('default_map', trans('validation.attributes.default_map'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('default_map', $maps, $settings['default_map'], ['class' => 'form-control', 'data-icon' => 'icon map']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('map_zoom_level', trans('validation.attributes.map_zoom_level'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('map_zoom_level', $zoom_levels, settings('main_settings.map_zoom_level'), ['class' => 'form-control', 'data-icon' => 'icon search']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('map_center_latitude', trans('validation.attributes.map_center_latitude'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('map_center_latitude', settings('main_settings.map_center_latitude'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('map_center_longitude', trans('validation.attributes.map_center_longitude'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('map_center_longitude', settings('main_settings.map_center_longitude'), ['class' => 'form-control']) !!}
            </div>
        </div>

        @include('Admin.MainServerSettings.partials.geocoder', [
                    'geocoder_apis' => $geocoder_apis,
                    'geo_index'     => 'primary'
                 ])

        @include('Admin.MainServerSettings.partials.geocoder', [
                    'geocoder_apis' => ['' => trans('global.primary')] + $geocoder_apis,
                    'geo_index'     => 'address'
                 ])

        <div class="form-group">
            {!! Form::label('geocoder_cache_enabled', trans('validation.attributes.geocoder_cache'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('geocoder_cache_enabled', $geocoder_cache_status, settings('main_settings.geocoder_cache_enabled'), ['class' => 'form-control']) !!}
            </div>
        </div>

        @if (settings('main_settings.geocoder_cache_enabled'))
            <div class="form-group">
                {!! Form::label('geocoder_cache_days', trans('validation.attributes.geocoder_cache_days'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
                <div class="col-xs-12 col-sm-8">
                    <div class="input-group">
                        {!! Form::select('geocoder_cache_days', $geocoder_cache_days, settings('main_settings.geocoder_cache_days'), ['class' => 'form-control', 'data-size' => '10']) !!}
                        <span class="input-group-btn">
                            <button class="btn btn-danger" type="button" onClick="$('#delete-geocoder-cache-form').submit();">
                                <i class="icon trash"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <div class="form-group">
            {!! Form::label('streetview_api', trans('validation.attributes.streetview_api'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('streetview_api', $streetview_apis, $streetview_api, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('streetview_key', trans('validation.attributes.streetview_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('streetview_key', $streetview_key, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('expire_notification[days_before]', trans('admin.expire_notification_before_days'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                <div class="input-group">
                    <div class="checkbox input-group-btn">
                        {!! Form::hidden('expire_notification[active_before]', 0) !!}
                        {!! Form::checkbox('expire_notification[active_before]', 1, settings('main_settings.expire_notification.active_before')) !!}
                        {!! Form::label(null, null) !!}
                    </div>
                    {!! Form::text('expire_notification[days_before]', settings('main_settings.expire_notification.days_before'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('expire_notification[days_after]', trans('admin.expire_notification_after_days'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                <div class="input-group">
                    <div class="checkbox input-group-btn">
                        {!! Form::hidden('expire_notification[active_after]', 0) !!}
                        {!! Form::checkbox('expire_notification[active_after]', 1, settings('main_settings.expire_notification.active_after')) !!}
                        {!! Form::label(null, null) !!}
                    </div>
                    {!! Form::text('expire_notification[days_after]', settings('main_settings.expire_notification.days_after'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="device_cameras_days" class="col-xs-12 col-sm-4 control-label">{{ trans('validation.attributes.device_cameras_days') }} (<span id="media-size"></span>)</label>
            <div class="col-xs-12 col-sm-8">
                {!! Form::input('number', 'device_cameras_days', settings('main_settings.device_cameras_days'), ['class' => 'form-control', 'min' => '1', 'max' => '180', 'step' => '1']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('extra_expiration_time', trans('validation.attributes.extra_expiration_time'), ['class' => 'col-xs-12 col-sm-4 control-label']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('extra_expiration_time', $extra_expiration_time_options, settings('main_settings.extra_expiration_time'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('captcha_provider', trans('validation.attributes.captcha_provider'), ['class' => 'col-xs-12 col-sm-4 control-label']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('captcha_provider', $captcha_providers, settings('main_settings.captcha_provider'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group" data-disablable="#captcha_provider;hide-disable;recaptcha">
            {!! Form::label('recaptcha_site_key', trans('validation.attributes.recaptcha_site_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('recaptcha_site_key', settings('main_settings.recaptcha_site_key'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group" data-disablable="#captcha_provider;hide-disable;recaptcha">
            {!! Form::label('recaptcha_secret_key', trans('validation.attributes.recaptcha_secret_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('recaptcha_secret_key', settings('main_settings.recaptcha_secret_key'), ['class' => 'form-control']) !!}
            </div>
        </div>
        {!! Form::close() !!}
        {!! Form::open(array('route' => 'admin.main_server_settings.delete_geocoder_cache', 'method' => 'POST', 'id' => 'delete-geocoder-cache-form')) !!}
        {!! Form::close() !!}
    </div>

    <div class="panel-footer">
        <button type="submit" class="btn btn-action" onClick="$('#main-settings-form').submit();">{{ trans('global.save') }}</button>
    </div>
</div>
@push('javascript')
<script>
    $(document).ready(function() {
        $(document).on('change', 'input[name^="available_maps"]', function () {
            $('.map-setting').hide();

            $('input[name^="available_maps"]:checked').each(function() {
                $('.map-setting-'+$(this).val()).show();
            });
        });

        $('input[name^="available_maps"]:first').trigger('change');


        $.ajax({
            type: 'GET',
            url: '{{ route('admin.media.size') }}',
            beforeSend: function() {},
            success: function(response) {
                $('#media-size').html(response);
            },
            complete: function () {}
        });
    });
</script>
@endpush