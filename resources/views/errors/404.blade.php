@extends('errors::layout')

@section('title', 'Page Not Found')

@section('message')
    {!! trans('errors.404') !!}
@endsection
