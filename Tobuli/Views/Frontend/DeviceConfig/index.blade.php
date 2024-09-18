@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon devices text-primary"></i> {{ trans('front.device_configuration') }}
@stop

@section('body')
{!!Form::open(['id' => 'device_config_form','route' => 'device_config.configure', 'method' => 'POST'])!!}
    @include('Frontend.DeviceConfig.form')
{!!Form::close()!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('front.configure')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop
