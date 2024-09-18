@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon money text-primary"></i> {{ trans('front.expenses') }}
@stop

@section('body')
    @include('Frontend.DeviceExpenses.index')
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop

