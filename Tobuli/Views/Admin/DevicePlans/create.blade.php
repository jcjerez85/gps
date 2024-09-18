@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon event"></i> {{ trans('global.add') }}
@stop

@section('body')
    {!!Form::open(['route' => 'admin.device_plan.store', 'method' => 'POST'])!!}
        @include('Admin.DevicePlans.form')
    {!! Form::close() !!}
@stop
