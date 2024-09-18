@extends('Frontend.Layouts.modal')

@section('title', trans('global.edit'))

@section('body')
    {!!Form::open(['route' => 'user_gprs_templates.update', 'method' => 'PUT'])!!}
        {!!Form::hidden('id', $item->id)!!}

        <div class="form-group">
            {!!Form::label('title', trans('validation.attributes.title').':')!!}
            {!!Form::text('title', $item->title, ['class' => 'form-control'])!!}
        </div>


        <div class="form-group">
            {!!Form::label('adapted', trans('validation.attributes.adapted').':')!!}
            {!! Form::select('adapted', $adaptedies, $item->adapted, ['class' => 'form-control']) !!}
        </div>

        @if(auth()->user()->perm('device.protocol', 'view'))
            <div class="form-group" data-disablable="[name='adapted'];hide-disable;protocol">
                {!!Form::label('protocol', trans('validation.attributes.device_protocol').':')!!}
                {!!Form::select('protocol', $protocols, $item->protocol, ['class' => 'form-control', 'data-live-search' => 'true'])!!}
            </div>
        @endif

        @if(auth()->user()->perm('devices', 'view'))
            <div class="form-group" data-disablable="[name='adapted'];hide-disable;devices">
                {!!Form::label('devices', trans('validation.attributes.devices').':')!!}
                {!! Form::select('devices[]', groupDevices($devices, auth()->user()), $item->devices, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
            </div>
        @endif

        @if(auth()->user()->perm('device.device_type_id', 'view'))
            <div class="form-group" data-disablable="[name='adapted'];hide-disable;device_types">
                {!!Form::label('device_types', trans('admin.device_types').':')!!}
                {!! Form::select('device_types[]', $device_types, $item->deviceTypes, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
            </div>
        @endif

        <div class="form-group">
            {!!Form::label('message', trans('validation.attributes.message').':')!!}
            {!!Form::textarea('message', $item->message, ['class' => 'form-control', 'rows' => 3])!!}
        </div>

        <div class="alert alert-info small">
            {!! trans('front.raw_command_supports') !!}
            <br><br>
            {!! trans('front.gprs_template_variables') !!}
        </div>
    {!!Form::close()!!}
@stop