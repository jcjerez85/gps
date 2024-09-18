@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon event"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!!Form::open(['route' => ['admin.device_plan.update', $item->id], 'method' => 'PUT'])!!}
        @include('Admin.DevicePlans.form')
    {!! Form::close() !!}
@stop
