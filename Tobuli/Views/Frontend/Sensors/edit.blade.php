@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon sensors"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(['route' => $route, 'method' => 'PUT']) !!}
        {!! Form::hidden('id', $item->id) !!}
        {!! Form::hidden('sensor_group_id', $item->group_id) !!}
        {!! Form::hidden('device_id', $item->device_id) !!}
    <div class="row">
        <div class="col-md-6 sen-data-fields">
            <div class="form-group">
                {!! Form::label('sensor_name', trans('validation.attributes.sensor_name').':') !!}
                {!! Form::text('sensor_name', $item->name, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('sensor_type', trans('validation.attributes.sensor_template').':') !!}
                {!! Form::select('sensor_type', $sensors, $item->type, ['class' => 'form-control', 'id' => 'sensor_type']) !!}
            </div>

            <div class="sensors_form_inputs engine_hours">
                <div class="form-group">
                    {!! Form::label('shown_value_by', trans('validation.attributes.shown_value_by').':') !!}
                    {!! Form::select('shown_value_by', ['connected' => trans('front.connected'), 'virtual' => trans('front.virtual_engine_hours'), 'logical' => trans('front.logical')], $item->shown_value_by, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group tag_name">
                @if (is_array($parameters))
                    {!! Form::label('tag_name', trans('validation.attributes.tag_name').':') !!}
                    <div class="input-group" id="sensor_parameters">
                        {!! Form::select('tag_name', $parameters, $item->tag_name, ['class' => 'form-control']) !!}
                    </div>
                @else
                    {!! Form::label('tag_name', trans('validation.attributes.tag_name').':') !!}
                    {!! Form::text('tag_name', $item->tag_name, ['class' => 'form-control']) !!}
                @endif
            </div>

            <div class="form-group unit_of_measurement">
                {!! Form::label('unit_of_measurement', trans('validation.attributes.unit_of_measurement').':') !!}
                {!! Form::text('unit_of_measurement', $item->unit_of_measurement, ['class' => 'form-control']) !!}
            </div>

            <div class="sensors_form_inputs acc harsh_acceleration harsh_breaking harsh_turning ignition door seatbelt engine logical rfid textual plugged">
                <div class="form-group">
                    {!! Form::label(null, trans('front.decbin').':') !!}
                    <div>
                        <div class="checkbox-inline">
                            {!! Form::hidden('decbin', 0) !!}
                            {!! Form::checkbox('decbin', 1, $item->decbin, ['class' => 'convert-bin']) !!}
                            {!! Form::label('decbin', 'DEC') !!}
                        </div>
                        <div class="checkbox-inline">
                            {!! Form::hidden('hexbin', 0) !!}
                            {!! Form::checkbox('hexbin', 1, $item->hexbin, ['class' => 'convert-bin']) !!}
                            {!! Form::label('hexbin', 'HEX') !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        {!! Form::checkbox('setflag', 1, $item->setflag) !!}
                        {!! Form::label('setflag', trans('front.setflag')) !!}
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs fuel_tank fuel_tank_calibration">
                <div class="form-group">
                    {!! Form::label('fuel_tank_name', trans('validation.attributes.fuel_tank_name').':') !!}
                    {!! Form::text('fuel_tank_name', $item->fuel_tank_name, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs fuel_tank">
                <div class="form-group">
                    {!! Form::label('parameters', trans('validation.attributes.parameters').':') !!}
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            {!! Form::text('full_tank', $item->full_tank, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.full_tank')]) !!}
                        </div>
                        <div class="col-md-6 col-sm-6">
                            {!! Form::text('full_tank_value', $item->full_tank_value, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs fuel_consumption">
                <div class="form-group">
                    {!! Form::label('shown_value_by', trans('validation.attributes.shown_value_by').':') !!}
                    {!! Form::select('shown_value_by', ['incremental' => trans('front.incremental'), 'separate' => trans('front.separate')], $item->shown_value_by, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs odometer">
                <div class="form-group">
                    {!! Form::label('odometer_value_by', trans('validation.attributes.odometer_value_by').':') !!}
                    {!! Form::select('odometer_value_by', ['connected_odometer' => trans('front.connected_odometer'), 'virtual_odometer' => trans('front.virtual_odometer')], $item->odometer_value_by, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs harsh_acceleration harsh_breaking harsh_turning notsetflag">
                <div class="form-group">
                    {!! Form::label('parameter_value', trans('validation.attributes.parameter_value').':') !!}
                    {!! Form::text('parameter_value', $item->on_value, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs harsh_acceleration harsh_breaking harsh_turning rfid textual setflag">
                <div class="form-group">
                    {!! Form::label('parameter_value', trans('validation.attributes.parameter_value').':') !!}
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('value_setflag_1', $item->value_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('value_setflag_2', $item->value_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs odometer_value_by virtual_odometer">
                <div class="form-group">
                    {!! Form::label('odometer_value', trans('validation.attributes.odometer_value').':') !!}
                    <div class="row">
                        <div class="col-xs-6">
                            {!! Form::text('odometer_value', $item->odometer_value, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-6">
                            {!! Form::select('odometer_value_unit', ['km' => trans('front.km'), 'mi' => trans('front.mi')], $item->odometer_value_unit, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs battery battery_external">
                <div class="form-group">
                    {!! Form::label('shown_value_by', trans('validation.attributes.shown_value_by').':') !!}
                    {!! Form::select('shown_value_by', ['tag_value' => trans('validation.attributes.tag_value'), 'min_max_values' => trans('front.min_max_values'), 'formula' => trans('validation.attributes.formula')], $item->shown_value_by, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs battery_value_by formula temperature temperature_calibration odometer_value_by connected_odometer load load_calibration tachometer numerical fuel_tank fuel_tank_calibration fuel_consumption speed_ecm">
                <div class="form-group">
                    {!! Form::label('formula', trans('validation.attributes.formula').':') !!}
                    <div class="input-group">
                        <div class="checkbox input-group-btn">
                            {!! Form::checkbox('formula_use', 1, ! empty($item->formula)) !!}
                            {!! Form::label(null) !!}
                        </div>
                        {!! Form::text('formula', (empty($item->formula) ? '[value]' : $item->formula), ['class' => 'form-control']) !!}
                    </div>
                    <span class="explanation">{{ trans('front.formula_example') }}</span>
                </div>
                <div class="alert alert-info" style="font-size: 12px;">
                    {{ trans('front.setflag_formula_info') }}
                </div>
            </div>

            <div class="sensors_form_inputs gsm battery_value_by min_max_values">
                <div class="form-group">
                    {!! Form::label('min_value', trans('validation.attributes.min_value').':') !!}
                    {!! Form::text('min_value', $item->min_value, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('max_value', trans('validation.attributes.max_value').':') !!}
                    {!! Form::text('max_value', $item->max_value, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs acc notsetflag">
                <div class="form-group">
                    {!! Form::label('on_value', trans('validation.attributes.on_value').':') !!}
                    {!! Form::text('on_value', $item->on_value, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('off_value', trans('validation.attributes.off_value').':') !!}
                    {!! Form::text('off_value', $item->off_value, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="sensors_form_inputs acc setflag">
                <div class="form-group">
                    {!! Form::label('on_value', trans('validation.attributes.on_value').':') !!}
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::text('on_setflag_1', $item->on_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('on_setflag_2', $item->on_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('on_setflag_3', $item->on_setflag_3, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_3')]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('off_value', trans('validation.attributes.off_value').':') !!}
                    <div class="row">
                        <div class="col-md-4">
                            {!! Form::text('off_setflag_1', $item->off_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('off_setflag_2', $item->off_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('off_setflag_3', $item->off_setflag_3, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_3')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs engine_hours_value_by ignition door seatbelt engine notsetflag drive_business drive_private logical route_color route_color_2 route_color_3 plugged counter">
                <div class="form-group">
                    {!! Form::label('on_type', trans('validation.attributes.on_value').':') !!}
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            {!! Form::select('on_type', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], $item->on_type, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-8 col-xs-4">
                            {!! Form::text('on_tag_value', $item->on_tag_value, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="sensors_form_inputs engine_hours_value_by ignition door seatbelt engine notsetflag drive_business drive_private logical route_color route_color_2 route_color_3 plugged">
                <div class="form-group">
                    {!! Form::label('off_type', trans('validation.attributes.off_value').':') !!}
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            {!! Form::select('off_type', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], $item->off_type, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-8 col-xs-4">
                            {!! Form::text('off_tag_value', $item->off_tag_value, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.tag_value')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs ignition door seatbelt engine logical setflag plugged">
                <div class="form-group">
                    {!! Form::label('on_value', trans('validation.attributes.on_value').':') !!}
                    <div class="row">
                        <div class="col-md-3">
                            {!! Form::select('on_type_setflag', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], $item->on_type, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('on_tag_setflag_1', $item->on_tag_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('on_tag_setflag_2', $item->on_tag_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('on_tag_setflag_3', $item->on_tag_setflag_3, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_3')]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('off_value', trans('validation.attributes.off_value').':') !!}
                    <div class="row">
                        <div class="col-md-3">
                            {!! Form::select('off_type_setflag', ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')], $item->off_type, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('off_tag_setflag_1', $item->off_tag_setflag_1, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_1')]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('off_tag_setflag_2', $item->off_tag_setflag_2, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_2')]) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('off_tag_setflag_3', $item->off_tag_setflag_3, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.on_setflag_3')]) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="sensors_form_inputs counter">
                <div class="form-group">
                    {!! Form::label('value', trans('validation.attributes.count').':') !!}
                    {!! Form::text('value', $item->getCounter(), ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="checkbox">
                            {!! Form::checkbox('add_to_history', 1, $item->add_to_history) !!}
                            {!! Form::label('add_to_history', trans('front.add_to_history')) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 sensors_form_inputs engine_hours">
                    <div class="form-group text-right">
                        <a href="javascript:" class="btn btn-action" data-url="{{ route('sensors.set_engine_hours', $item->device_id) }}" data-modal="set_engine_hours">
                            <i class="icon engine_hours"></i> {{ trans('front.engine_hours') }}
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 sensors_form_inputs battery battery_external gsm odometer engine_hours temperature tachometer numerical load speed_ecm">
                    <div class="form-group">
                        <div class="checkbox">
                            {!! Form::hidden('skip_empty', 0) !!}
                            {!! Form::checkbox('skip_empty', 1, $item->skip_empty) !!}
                            {!! Form::label('skip_empty', trans('validation.attributes.skip_empty')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 sen-cal-fields">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('skip_calibration', 0) !!}
                    {!! Form::checkbox('skip_calibration', 1, $item->skip_calibration) !!}
                    {!! Form::label('skip_calibration', trans('validation.attributes.skip_calibration')) !!}
                </div>
            </div>

            {!! Form::label(null, trans('front.calibration')) !!}
            {!! Form::hidden('calibrations_fake') !!}
            <div style="display: block; height: 400px;overflow-y: scroll; border: 1px solid #dddddd; margin-bottom: 20px;">
                <table class="table">
                    <thead>
                        <th style="font-weight: normal">{{ trans('validation.attributes.tag_value') }}</th>
                        <th style="font-weight: normal">{{ trans('front.calibrated_value') }}</th>
                        <th></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-5">
                        {!! Form::label('x',trans('validation.attributes.tag_value')) !!}
                        {!! Form::text('x', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    <div class="col-xs-5">
                        {!! Form::label('y',trans('front.calibrated_value')) !!}
                        {!! Form::text('y', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    <div class="col-xs-2">
                        {!! Form::label(null,'&nbsp;') !!}
                        <a href="javascript:" class="btn btn-action btn-block add_calibration" type="button"><i class="icon add" title="{{ trans('global.add') }}"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="calibrations" style="display: none;">{{ json_encode($item->calibrations) }}</span>
    {!! Form::close() !!}
    <script>
        $(document).ready(function() {
            app.sensors.inputs($('#sensors_edit'));
        });
    </script>
@stop