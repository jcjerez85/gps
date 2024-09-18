@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon"></i> {{ trans('global.add') }}
@stop

@section('body')
    {!!Form::open(['route' => 'admin.device_type.store', 'method' => 'POST'])!!}
        @include('Admin.DeviceTypes.form')
    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update_with_files">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop