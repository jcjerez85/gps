@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon sharing"></i> {{ trans('front.sharing') }}
@stop

@section('body')
    {!! Form::open(['route' => 'sharing.store', 'method' => 'POST']) !!}
        <div class="form-group">
            <div class="checkbox-inline">
                {!! Form::hidden('active', 0) !!}
                {!! Form::checkbox('active', 1, true) !!}
                {!! Form::label(null, trans('validation.attributes.active')) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name').':') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}
            <div class="input-group">
                <div class="checkbox input-group-btn">
                    {!! Form::hidden('enable_expiration_date', 0) !!}
                    {!! Form::checkbox('enable_expiration_date', 1, false, ['data-disabler' => '#expiration_date;disable']) !!}
                    {!! Form::label(null) !!}
                </div>
                {!! Form::text('expiration_date', null, ['class' => 'form-control datetimepicker']) !!}
            </div>
        </div>
    <div class="form-group">
        {!! Form::label('devices', trans('validation.attributes.devices').'*:') !!}
        {!! Form::select('devices[]', $devices, null, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
    </div>
    {!! Form::close() !!}
@stop
