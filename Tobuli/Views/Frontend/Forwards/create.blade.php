@extends('Frontend.Layouts.modal')

@section('title', trans('front.add'))

@section('body')
    {!!Form::open(['route' => 'forwards.store', 'method' => 'POST'])!!}
        {!!Form::hidden('id')!!}
        @include('Frontend.Forwards.form')
    {!!Form::close()!!}
@stop