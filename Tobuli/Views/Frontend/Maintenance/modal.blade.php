@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon services"></i> {!!trans('front.services')!!}
@stop

@section('body')
    @include('Frontend.Maintenance.table')
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.close')!!}</button>
@stop