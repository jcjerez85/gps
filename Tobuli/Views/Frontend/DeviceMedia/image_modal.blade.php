@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon camera"></i> {!! $camera_name !!}
@stop

@section('body')
    <img class="center-block" src="{{ $image->path }}">
@stop

@section('buttons')
@stop
