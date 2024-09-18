@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-sm')

@section('title')
    <i class="icon call_action"></i> {{ trans('front.call_actions') }}
@stop

@section('body')
    {!!Form::open(['route' => 'call_actions.store', 'method' => 'POST'])!!}
        @include('Frontend.CallAction.form')
    {!!Form::close()!!}
@stop
