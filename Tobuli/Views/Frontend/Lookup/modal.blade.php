@extends('Frontend.Layouts.modal')

@section('modal_class')
modal-full
@stop

@section('title')
    <i class="{{ $lookup->getIcon() }}"></i> {{ $lookup->getTitle() }}
@stop

@section('body')
    @include('front::Lookup.partials.table')
@stop

@section('buttons')
@stop