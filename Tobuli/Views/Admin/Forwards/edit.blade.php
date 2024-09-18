@extends('Frontend.Layouts.modal')

@section('title', trans('global.edit'))

@section('body')
    {!!Form::open(['route' => ['admin.forwards.update', $item->id], 'method' => 'PUT'])!!}
        @include('Admin.Forwards.form')
    {!!Form::close()!!}
@stop