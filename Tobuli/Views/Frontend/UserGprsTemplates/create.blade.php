@extends('Frontend.Layouts.modal')

@section('title', trans('front.add_template'))

@section('body')
    {!!Form::open(['route' => 'user_gprs_templates.store', 'method' => 'POST'])!!}
        {!!Form::hidden('id')!!}
        <div class="form-group">
            {!!Form::label('title', trans('validation.attributes.title').':')!!}
            {!!Form::text('title', null, ['class' => 'form-control'])!!}
        </div>

        <div class="form-group">
            {!!Form::label('adapted', trans('validation.attributes.adapted').':')!!}
            {!! Form::select('adapted', $adaptedies, null, ['class' => 'form-control']) !!}
        </div>

        @if(auth()->user()->perm('device.protocol', 'view'))
        <div class="form-group" data-disablable="[name='adapted'];hide-disable;protocol">
            {!!Form::label('protocol', trans('validation.attributes.device_protocol').':')!!}
            {!!Form::select('protocol', $protocols, null, ['class' => 'form-control', 'data-live-search' => 'true'])!!}
        </div>
        @endif

        @if(auth()->user()->perm('devices', 'view'))
            <div class="form-group" data-disablable="[name='adapted'];hide-disable;devices">
                {!!Form::label('devices', trans('validation.attributes.devices').':')!!}
                {!! Form::select('devices[]', groupDevices($devices, auth()->user()), null, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
            </div>
        @endif

        @if(auth()->user()->perm('device.device_type_id', 'view'))
            <div class="form-group" data-disablable="[name='adapted'];hide-disable;device_types">
                {!!Form::label('device_types', trans('validation.attributes.device_type_id').':')!!}
                {!! Form::select('device_types[]', $device_types, null, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
            </div>
        @endif

        <div class="form-group">
            {!!Form::label('message', trans('validation.attributes.message').':')!!}
            {!!Form::textarea('message', null, ['class' => 'form-control', 'rows' => 3])!!}
        </div>

        <div class="alert alert-info small">
            {!! trans('front.raw_command_supports') !!}
            <br><br>
            {!! trans('front.gprs_template_variables') !!}
        </div>
    {!!Form::close()!!}
@stop