@extends('Frontend.Layouts.modal')

@section('title', trans('front.add'))

@section('body')
    {!!Form::open(['route' => 'admin.forwards.store', 'method' => 'POST'])!!}
        {!!Form::hidden('id')!!}
        @include('Admin.Forwards.form')
    {!!Form::close()!!}
@stop