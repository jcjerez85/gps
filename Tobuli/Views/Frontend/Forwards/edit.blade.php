@extends('Frontend.Layouts.modal')

@section('title', trans('global.edit'))

@section('body')
    {!!Form::open(['route' => ['forwards.update', $item->id], 'method' => 'PUT'])!!}
        @include('Frontend.Forwards.form')
    {!!Form::close()!!}
@stop