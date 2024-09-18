@extends('Admin.Layouts.modal')

@section('title')
    <i class="icon edit"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.device_config.update', 'method' => 'PUT']) !!}
        {!!Form::hidden('id', $item->id)!!}
        <div class="checkbox">
            {!! Form::hidden('active', 0) !!}
            {!! Form::checkbox('active', 1, $item->active) !!}
            {!! Form::label('active', trans('validation.attributes.active') ) !!}
        </div>
        <div class="form-group">
            {!! Form::label('brand', trans('validation.attributes.brand')) !!}
            {!! Form::text('brand', $item->brand, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('model', trans('front.model')) !!}
            {!! Form::text('model', $item->model, ['class' => 'form-control']) !!}
        </div>
        @if ($item->edited)
        <div class="checkbox">
            {!! Form::hidden('use_default', 0) !!}
            {!! Form::checkbox('use_default', 1, false) !!}
            {!! Form::label('use_default', trans('global.use_default') ) !!}
        </div>
        @endif

        @include("Admin.DeviceConfig.partials._commands_panel", ['commands' => $commands])
    {!! Form::close() !!}

    <div class="hidden dummy-command" disabled>
        @include("Admin.DeviceConfig.partials._command")
    </div>
@stop
