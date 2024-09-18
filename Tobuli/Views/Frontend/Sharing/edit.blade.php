@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon sharing"></i> {{ trans('front.sharing') }}
@stop

@section('body')
    {!! Form::open(['route' => ['sharing.update', 'sharing_id' => $sharing->id], 'method' => 'PUT']) !!}
        <div class="form-group">
            <div class="checkbox-inline">
                {!! Form::hidden('active', 0) !!}
                {!! Form::checkbox('active', 1, $sharing->active) !!}
                {!! Form::label(null, trans('validation.attributes.active')) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name').':') !!}
            {!! Form::text('name', $sharing->name, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}
            <div class="input-group">
                <div class="checkbox input-group-btn">
                    {!! Form::hidden('enable_expiration_date', 0) !!}
                    {!! Form::checkbox(
                        'enable_expiration_date',
                        1,
                        $sharing->expiration_date ? true : false,
                        ['data-disabler' => '#expiration_date;disable'])
                    !!}
                    {!! Form::label(null) !!}
                </div>
                {!! Form::text('expiration_date', $sharing->expiration_date ? Formatter::time()->convert($sharing->expiration_date) : null, ['class' => 'form-control datetimepicker']) !!}
            </div>
        </div>

        <div class="form-group">
            <div class="checkbox">
                {!! Form::hidden('delete_after_expiration', 0) !!}
                {!! Form::checkbox('delete_after_expiration', 1, $sharing->delete_after_expiration) !!}
                {!! Form::label('delete_after_expiration', trans('validation.attributes.delete_after_expiration') ) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('devices', trans('validation.attributes.devices').'*:') !!}
            {!! Form::select('devices[]', $devices, $sharing->devices->pluck('id', 'id')->all(), ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
        </div>
    {!! Form::close() !!}
@stop
