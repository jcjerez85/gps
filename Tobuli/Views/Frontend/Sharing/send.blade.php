@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon sharing"></i> {{ trans('front.sharing') }}
@stop

@section('body')
    @include('Frontend.Sharing.send_form')
@stop
