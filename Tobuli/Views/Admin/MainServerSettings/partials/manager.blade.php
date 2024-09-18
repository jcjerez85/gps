<div class="panel panel-default">

    <div class="panel-heading">
        <div class="panel-title"><i class="icon setup"></i> {{ trans('front.settings') }}</div>
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
            {!! Form::label('map_zoom_level', trans('validation.attributes.map_zoom_level'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('map_zoom_level', $zoom_levels, $settings['map_zoom_level'], ['class' => 'form-control', 'data-icon' => 'icon search']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('map_center_latitude', trans('validation.attributes.map_center_latitude'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('map_center_latitude', $settings['map_center_latitude'], ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('map_center_longitude', trans('validation.attributes.map_center_longitude'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('map_center_longitude', $settings['map_center_longitude'], ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('noreply_email', trans('validation.attributes.noreply_email'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('noreply_email', $settings['noreply_email'] ?? '', ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('from_name', trans('validation.attributes.from_name'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('from_name', $settings['from_name'] ?? '', ['class' => 'form-control']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

    <div class="panel-footer">
        <button type="submit" class="btn btn-action" onClick="$('#main-settings-form').submit();">{{ trans('global.save') }}</button>
    </div>
</div>