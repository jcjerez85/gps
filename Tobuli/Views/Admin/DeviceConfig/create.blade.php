@extends('Admin.Layouts.modal')

@section('title')
    <i class="icon edit"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.device_config.store', 'method' => 'POST']) !!}
        <div class="checkbox">
            {!! Form::hidden('active', 0) !!}
            {!! Form::checkbox('active', 1, true) !!}
            {!! Form::label('active', trans('validation.attributes.active') ) !!}
        </div>
        <div class="form-group">
            {!! Form::label('brand', trans('validation.attributes.brand')) !!}
            {!! Form::text('brand', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('model', trans('front.model')) !!}
            {!! Form::text('model', null, ['class' => 'form-control']) !!}
        </div>

        @include("Admin.DeviceConfig.partials._commands_panel", ['commands' => []])
    {!! Form::close() !!}

    <div class="hidden dummy-command" disabled>
        @include("Admin.DeviceConfig.partials._command")
    </div>
@stop
