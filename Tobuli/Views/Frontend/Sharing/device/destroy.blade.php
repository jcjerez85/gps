@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.delete') }}
@stop

@section('body')
    {!! Form::open(['route' => ['sharing_device.destroy', 'sharing_id' => $sharing_id, 'device_id' => $device_id], 'method' => 'DELETE']) !!}
        {!! trans('front.do_delete') !!}
    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{{ trans('global.yes') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.no') }}</button>
@stop
