@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!!Form::open(['route' => ['admin.device_type_imei.update', $item->id], 'method' => 'PUT'])!!}
        @include('Admin.DeviceTypeImei.form')
    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update_with_files">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop