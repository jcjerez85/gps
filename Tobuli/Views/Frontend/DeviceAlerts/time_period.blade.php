@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-sm')

@section('title')
    <i class="icon time"></i> {{ trans('front.time_period') }}
@stop

@section('body')
    {!!Form::open(['route' => ['device.alerts.time_period.update', $device->id, $alert->id], 'method' => 'POST'])!!}
    <div class="form-group">
        {!! Form::label('date_from', trans('validation.attributes.date_from').':') !!}
        <div class="input-group">
            <div class="checkbox input-group-btn">
                {!! Form::hidden('enable_date_from', 0) !!}
                {!! Form::checkbox('enable_date_from', 1, $alert->pivot->active_from ? true : false, ['data-disabler' => '#date_from;disable']) !!}
                {!! Form::label(null) !!}
            </div>
            {!! Form::text('date_from', $alert->pivot->active_from ? Formatter::time()->convert($alert->pivot->active_from ) : null, ['class' => 'form-control datetimepicker', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('date_to', trans('validation.attributes.date_to').':') !!}
        <div class="input-group">
            <div class="checkbox input-group-btn">
                {!! Form::hidden('enable_date_to', 0) !!}
                {!! Form::checkbox('enable_date_to', 1, $alert->pivot->active_to ? true : false, ['data-disabler' => '#date_to;disable']) !!}
                {!! Form::label(null) !!}
            </div>
            {!! Form::text('date_to', $alert->pivot->active_to ? Formatter::time()->convert($alert->pivot->active_to ) : null, ['class' => 'form-control datetimepicker', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    {!!Form::close()!!}
@stop
